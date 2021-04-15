<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RevertQuoteInventory
 * @package Udeytech\Productkit\Model\Observer
 */
class RevertQuoteInventory implements ObserverInterface
{
    /**
     * @var Stock
     */
    protected $_stockFilter;

    /**
     * RevertQuoteInventory constructor.
     * @param Stock $stockFilter
     */
    public function __construct(
        Stock $stockFilter
    )
    {
        $this->_stockFilter = $stockFilter;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $items = $this->_getProductsQty($quote->getAllItems());
        $this->_stockFilter->create()->revertProductsSale($items);
        $quote->setKitInventoryProcessed(false);
    }

    /**
     * @param $relatedItems
     * @return array
     */
    protected function _getProductsQty($relatedItems){
        $items = array();
        foreach ($relatedItems as $item) {
            $productId = $item->getProductId();
            if (!$productId) continue;
            $qty = $item->getQty();
            if ($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($item->getSku())) {
                $product = $item->getProduct();
                $optionIds = $item->getOptionByCode('option_ids');
                if ($optionIds) {
                    foreach (explode(',', $optionIds->getValue()) as $optionId) {
                        $option = $product->getOptionById($optionId);
                        if ($option AND 'products' == $option->getTitle()) {
                            $itemOption = $item->getOptionByCode('option_' . $option->getId());
                            $selectedProductIds = unserialize($itemOption->getValue());
                            foreach ($selectedProductIds as $selectedProductId) {
                                if (isset($items[$selectedProductId])) {
                                    $items[$selectedProductId]['qty'] += $qty;
                                } else {
                                    $items[$selectedProductId] = array(
                                        'qty' => $qty,
                                        'item' => null,
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return $items;
    }
}
