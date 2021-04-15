<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Udeytech\Productkit\Model\Source\Kit\Type;

/**
 * Class CancelOrderItem
 * @package Udeytech\Productkit\Model\Observer
 */
class CustomerQuoteMerge implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var Stock
     */
    protected $_stockFilter;
    /**
     * CancelOrderItem constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     */
    protected $_registry;
    /**
     * @var SessionFactory
     */
    protected $_sessionFactory;

    /**
     * CustomerQuoteMerge constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     * @param SessionFactory $sessionFactory
     * @param Registry $registry
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter,
        SessionFactory $sessionFactory,
        Registry $registry)
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
        $this->_registry = $registry;
        $this->_sessionFactory = $sessionFactory;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerQuote = $observer->getEvent()->getQuote();
        $visitorQuote = $observer->getEvent()->getSource();
        foreach ($visitorQuote->getAllItems() as $visitorQuoteItem) {
            $name = $this->helper('Udeyetch\Productkit\Helper\Data')->getKitNameBySku($visitorQuoteItem->getSku());
            $kitType = $this->helper('Udeyetch\Productkit\Helper\Data')->getKitType($name);
            if (Type::KIT_TYPE_FREE == $kitType) {
                $found = false;
                foreach ($customerQuote->getAllItems() as $customerQuoteItem) {
                    $name = $this->helper('Udeyetch\Productkit\Helper\Data')->getKitNameBySku($customerQuoteItem->getSku());
                    $kitType = $this->helper('Udeyetch\Productkit\Helper\Data')->getKitType($name);
                    //if ($customerQuoteItem->getSku() == 'product_kit_free')
                    if (Type::KIT_TYPE_FREE == $kitType) {
                        $customerQuote->removeItem($customerQuoteItem->getId());
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    $_msg = __('You already have a Free Product Kit in your Cart. It was updated with the last one created.');
                    $this->_sessionFactory->addNotice($_msg);
                }
                break;
            }
        }
    }
}
