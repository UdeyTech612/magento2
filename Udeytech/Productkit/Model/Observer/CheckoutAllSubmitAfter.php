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
use Magento\Indexer\Model\IndexerFactory;

/**
 * Class CheckoutAllSubmitAfter
 * @package Udeytech\Productkit\Model\Observer
 */
class CheckoutAllSubmitAfter implements ObserverInterface
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
    protected $_indexerFactory;

    /**
     * CheckoutAllSubmitAfter constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter,
        IndexerFactory $indexerFactory
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
        $this->_indexerFactory = $indexerFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote->getKitInventoryProcessed()) {
            $this->subtractQuoteInventory($observer);
            $this->reindexQuoteInventory($observer);
        }
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function subtractQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getKitInventoryProcessed()) return;
        $items = $this->_getProductsQty($quote->getAllItems());
        $this->_itemsForReindex = $this->_stockFilter->registerProductsSale($items);
        $quote->setKitInventoryProcessed(true);
        return $this;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function reindexQuoteInventory($observer)
    {
        $quote = $observer->getEvent()->getQuote();
//        $productIds = array();
//        foreach ($quote->getAllItems() as $item) {
//            if ($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($item->getSku())) {
//                $product = $item->getProduct();
//                $optionIds = $item->getOptionByCode('option_ids');
//                if ($optionIds) {
//                    foreach (explode(',', $optionIds->getValue()) as $optionId) {
//                        $option = $product->getOptionById($optionId);
//                        if ($option AND 'products' == $option->getTitle()) {
//                            $itemOption = $item->getOptionByCode('option_' . $option->getId());
//                            $selectedProductIds = unserialize($itemOption->getValue());
//                            foreach ($selectedProductIds as $selectedProductId) {
//                                $productIds[$selectedProductId] = $selectedProductId;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//        $this->_indexerFactory->create()->reindexProducts($productIds);
//        $productIds = array();
//        foreach ($this->_itemsForReindex as $item) {
//            $item->save();
//            $productIds[] = $item->getProductId();
//        }
//        $this->_indexerFactory->create()->reindexProductIds($productIds);
//        $this->_itemsForReindex = array();
        return $this;
    }
}
