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

/**
 * Class CancelOrderItem
 * @package Udeytech\Productkit\Model\Observer
 */
class SaveKitPriceFormData implements ObserverInterface
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
     * SaveKitPriceFormData constructor.
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
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $controllerAction = $observer->getEvent()->getControllerAction();
        $cartData = $controllerAction->getRequest()->getParam('cart');
        $kitPrices = array();
        if(is_array($cartData)) {
            foreach($cartData as $itemId => $itemData) {
                if(isset($itemData['price'])) {
                    $kitPrices[$itemId] = $itemData['price'];
                }
            }
        }
        $this->_registry->registry('product_kit_prices_data', new \Magento\Framework\DataObject($kitPrices));
    }
}
