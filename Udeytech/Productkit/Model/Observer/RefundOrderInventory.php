<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;


use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class RefundOrderInventory
 * @package Udeytech\Productkit\Model\Observer
 */
class RefundOrderInventory implements ObserverInterface
{

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;
    /**
     * @var
     */
    protected $_stockFilter;

    /**
     * RefundOrderInventory constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param SessionManagerInterface $coreSession
     * @param Stock $stockFilter
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        SessionManagerInterface $coreSession,
        Stock $stockFilter
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_coreSession = $coreSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = array();
        foreach ($creditmemo->getAllItems() as $cmItem) {
            $return = false;
            if ($cmItem->hasBackToStock()) {
                if ($cmItem->getBackToStock() AND $cmItem->getQty()) {
                    $return = true;
                }
            } elseif (Mage::helper('cataloginventory')->isAutoReturnEnabled()) {
                $return = true;
            }
            if ($return) {
                $orderItem = $cmItem->getOrderItem();
                if ($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($orderItem->getSku())) {
                    $product = $this->_productCollectionFactory->create()->setStoreId($orderItem->getStoreId())->load($orderItem->getProductId());
                    $productOptions = $product->getOptions();
                    $kitOptionId = 0;
                    foreach ($productOptions as $_option) {
                        if ('products' == $_option->getTitle()) {
                            $kitOptionId = $_option->getId();
                            break;
                        }
                    }
                    $orderProductOptions = $orderItem->getProductOptions();
                    if ($kitOptionId AND isset($orderProductOptions['info_buyRequest']['options'][$kitOptionId])) {
                        $selectedProductIds = unserialize($orderProductOptions['info_buyRequest']['options'][$kitOptionId]);
                        foreach ($selectedProductIds as $selectedProductId) {
                            if (isset($items[$selectedProductId])) {
                                $items[$selectedProductId]['qty'] += $cmItem->getQty();
                            } else {
                                $items[$selectedProductId] = array(
                                    'qty' => $cmItem->getQty(),
                                    'item' => null,
                                );
                            }
                        }
                    }
                }
            }
        }
        $this->_stockFilter->create()->revertProductsSale($items);
    }
}
