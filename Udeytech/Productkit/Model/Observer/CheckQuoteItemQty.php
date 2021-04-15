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
 * Class CheckQuoteItemQty
 * @package Udeytech\Productkit\Model\Observer
 */
class CheckQuoteItemQty implements ObserverInterface
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
        Stock $stockFilter
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
    }
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote() || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }
        $kitQty = $quoteItem->getQty();
        $items = array();
        //if ($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($quoteItem->getSku())) {
        $product = $quoteItem->getProduct();
        $optionIds = $quoteItem->getOptionByCode('option_ids');
        if ($optionIds) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option AND 'products' == $option->getTitle()) {
                    $itemOption = $quoteItem->getOptionByCode('option_' . $option->getId());
                    $selectedProductIds = unserialize($itemOption->getValue());
                    foreach ($selectedProductIds as $selectedProductId) {
                        if (!isset($items[$selectedProductId])) {
                            $items[$selectedProductId] = 0;
                        }
                        $items[$selectedProductId] += $kitQty;
                    }
                }
            }
        }
        // }
        foreach ($items as $selectedProductId => $selectedQty) {
            $subProduct = $this->_productCollectionFactory->create()->load($selectedProductId);
            $stockItem = $subProduct->getStockItem();
            $result = $stockItem->checkQuoteItemQty($selectedQty, $selectedQty, $selectedQty);
            if ($result->getHasError()) {
                $quoteItem->setHasError(true)->addMessage($result->getMessage());
                $quoteItem->getQuote()->setHasError(true)->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
            }
        }
        return $this;
    }
}
