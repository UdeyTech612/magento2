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
use Magento\Framework\Registry;
use Uedytech\Productkit\Model\Source\Kit\Type;

/**
 * Class CancelOrderItem
 * @package Udeytech\Productkit\Model\Observer
 */
class UpdateCartKitPrice implements ObserverInterface
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
     * UpdateCartKitPrice constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     * @param Registry $registry
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter,
        Registry $registry
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
        $this->_registry = $registry;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $cart = $observer->getEvent()->getCart();
        $kitPrices = $this->_registry->registry('product_kit_prices_data');
        if ($kitPrices) {
            foreach ($kitPrices->getData() as $itemId => $itemPrice) {
                $item = $cart->getQuote()->getItemById($itemId);
                if (!$item) {
                    continue;
                }
                $name = $this->_helper()->getKitNameBySku($item->getProduct()->getSku());
                $kitType = $this->helper('Udeytech\Productkit\Helper\Data')->getKitType($name);
                if (Type::KIT_TYPE_FREE != $kitType) {
                    continue;
                }
                $buyRequestOption = $item->getOptionByCode('info_buyRequest');
                $buyRequest = unserialize($buyRequestOption->getValue());
                $buyRequest['price'] = $itemPrice;
                $buyRequestOption->setValue(serialize($buyRequest));
            }
        }
        $this->_registry->unregister('product_kit_prices_data');
    }
}
