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

/**
 * Class CancelOrderItem
 * @package Udeytech\Productkit\Model\Observer
 */
class CancelOrderItem implements ObserverInterface
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
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter)
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderItem = $observer->getEvent()->getItem();
//        $qty = $orderItem->getQtyOrdered() - max($orderItem->getQtyShipped(), $orderItem->getQtyInvoiced()) - $orderItem->getQtyCanceled();
//        if ($orderItem->getId() AND ($productId = $orderItem->getProductId()) AND $qty) {
//            if ($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($orderItem->getSku())) {
//                $product = $this->_productCollectionFactory->setStoreId($orderItem->getStoreId())->load($orderItem->getProductId());
//                $productOptions = $product->getOptions();
//                $kitOptionId = 0;
//                foreach ($productOptions as $_option) {
//                    if ('products' == $_option->getTitle()) {
//                        $kitOptionId = $_option->getId();
//                        break;
//                    }
//                }
//                $orderProductOptions = $orderItem->getProductOptions();
//                if ($kitOptionId AND isset($orderProductOptions['info_buyRequest']['options'][$kitOptionId])) {
//                    $selectedProductIds = unserialize($orderProductOptions['info_buyRequest']['options'][$kitOptionId]);
//                    foreach ($selectedProductIds as $selectedProductId) {
//                        $this->_stockFilter->backItemQty($selectedProductId, $qty);
//                    }
//                }
//            }
//        }
        return $this;
    }
}
