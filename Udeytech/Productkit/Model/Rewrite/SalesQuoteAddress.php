<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Framework\Model\Context;
/**
 * Class SalesQuoteAddress
 * @package Udeytech\Productkit\Model\Rewrite
 */
class SalesQuoteAddress extends \Magento\Quote\Model\Quote\Address
{
    protected $_productCollection;
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        array $data = []
){      $this->_registry = $registry;
        $this->_productCollection = $productCollection;
        parent::_construct($context,$data);
    }
    public function collectShippingRates()
    {
        parent::collectShippingRates();
        $onlyFreeKit = true;
        $hasFreeKit = false;
        foreach ($this->getAllItems() as $item){
            $name = $this->_helper()->getKitNameBySku( $item->getSku() );
            $kitType = $this->_helper()->getKitType( $name );
            if (\Udeytech\Productkit\Model\Source\Kit\Type::KIT_TYPE_FREE == $kitType){
                $hasFreeKit = true;
                  } else {
                  $onlyFreeKit = false;
                }
              }
        if(!$hasFreeKit OR !$onlyFreeKit){
            foreach ($this->getShippingRatesCollection() as $rate){
                if (!$rate->isDeleted()) {
                    if ('usps' == $rate->getCarrier() AND false !== stripos($rate->getMethodTitle(), 'envelope')){
                        $rate->isDeleted(true);
                    }
                }
            }
        }
        return $this;
    }
    public function getAllItems() {
        $items = parent::getAllItems();
        foreach ($items as $item){
            if($this->_helper()->isKitProduct($item->getSku())){
                if (!$item->getFlagWeightIsCalculated()){
                    $item->setWeight($this->_calculateKitWeightItem($item));
                    $item->setFlagWeightIsCalculated(true);
                }
            }
        }
        return $items;
    }
    protected function _calculateKitWeightItem($item) {
        $weight = 0;
        $product = $item->getProduct();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds){
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option AND 'products' == $option->getTitle()){
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $selectedProductIds = unserialize($itemOption->getValue());
                    $selectedProduct = $this->_productCollection;
                    foreach($selectedProductIds as $_productId) {
                        $selectedProduct->load($_productId);
                        $weight += $selectedProduct->getWeight();
                    }
                }
            }
        }
        return $weight;
    }
    protected function _helper(){
        return $this->helper('Udeytech\Productkit\Helper\Data');
    }
    public function unsetCachedItems(){
        $this->unsetData('cached_items_all');
        $this->unsetData('cached_items_nominal');
        $this->unsetData('cached_items_nonnominal');
        return $this;
    }
}
