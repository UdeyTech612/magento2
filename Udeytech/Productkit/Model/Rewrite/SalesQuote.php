<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Catalog\Model\ResourceModel\Collection;
use Magento\Checkout\Model\Session;
use Magento\Framework\Model\Context;
class SalesQuote extends \Magento\Quote\Model\Quote {
        protected $_checkoutSession;
        protected $_productCollection;
        protected $_registry;
        public function __construct(
         Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        array $data = []){
        $this->_productCollection = $productCollection;
        $this->_checkoutSession = $checkoutSession;
        $this->_registry = $registry;
        parent::_construct($context,$data);
        }
//        protected function _beforeSave(){
//        parent::_beforeSave();
//        $checkoutSession = $this->_checkoutSession;
//        $freeKitItems = array();
//        foreach ($this->getAllItems() as $item) {
//            $name = $this->_helper()->getKitNameBySku( $item->getSku() );
//            $kitType = $this->_helper()->getKitType( $name );
//            if (\Udeytech\Productkit\Model\Source\Kit\Type::KIT_TYPE_FREE == $kitType){
//                $freeKitItems[$item->getId()] = $item->getQty();
//            }
//        }
//        if ($freeKitItems){
//            ksort($freeKitItems, SORT_NUMERIC);
//            $someWasRemoved = false;
//            while (count($freeKitItems) > 1){
//                $itemId = key($freeKitItems);
//                  foreach ($this->getAllItems() as $item){
//                    if ($item->getId() == $itemId){
//                        $item->isDeleted(true);
//                        foreach($item->getOptions() as $itemOption){
//                            $itemOption->isDeleted(true);
//                        }
//                    }
//                  }
//                $someWasRemoved = true;
//                unset($freeKitItems[$itemId]);
//            }
//            if ($someWasRemoved){
//                $checkoutSession->addNotice($this->helper('Udeytech\Productkit\Helper\Data')->__('Only the latest Free kit is allowed in the cart.'));
//            }
//            reset($freeKitItems);
//            $itemId = key($freeKitItems);
//            //search for item
//            foreach ($this->getAllItems() as $item) {
//                if ($item->getId() == $itemId) {
//                    if ($item->getQty() > 1) {
//                        $item->setQty(1);
//                        $checkoutSession->addNotice($this->helper('Udeytech\Productkit\Helper|Data')->__('Free kit qty was reset to 1.'));
//                    }
//                }
//            }
//        }
//        foreach ($this->getAllItems() as $item) {
//            if(!$this->_helper()->isKitProduct($item->getSku()))
//                continue;
//            $name = $this->_helper()->getKitNameBySku($item->getSku());
//            $kitType = $this->_helper()->getKitType($name);
//            if (\Udeytech\Productkit\Model\Source\Kit\Type::KIT_TYPE_FREE == $kitType)
//                continue;
//            $productId = $this->_productCollection->create()->getIdBySku($item->getSku());
//            $product = $this->_productCollection->create()->load($productId);
//            $prodQtyShouldBeInKit = $product->getProductkitKitQty();
//            $prodQtyRealInKit = 0;
//            $options = array();
//            $optionIds = $item->getOptionByCode('option_ids');
//            if($optionIds){
//                foreach (explode(',', $optionIds->getValue()) as $optionId){
//                    $option = $product->getOptionById($optionId);
//                    if ($option AND 'products' == $option->getTitle()){
//                        $itemOption = $item->getOptionByCode('option_' . $option->getId());
//                        $selectedProductIds = unserialize($itemOption->getValue());
//                        $selectedProduct = $this->_productCollection->create();
//                        foreach($selectedProductIds as $_productId){
//                            $selectedProduct->load($_productId);
//                            if($selectedProduct->getId())
//                                $prodQtyRealInKit ++;
//                        }
//                    }
//                }
//            }
//        if($prodQtyShouldBeInKit != $prodQtyRealInKit){
//            $item->isDeleted(true);
//            foreach($item->getOptions() as $itemOption){
//                $itemOption->isDeleted(true);
//            }
//            $checkoutSession->addError($this->helper('Udeytech\Productkit\Helper\Data')->__('Not all products exist in the kit. Kit was removed.'));
//           }
//        }
//        }
//        /**
//        * @return mixed
//        */
//        public function getAllItems()
//        {
//        $items = parent::getAllItems();
//        foreach ($items as $item)
//        {
//            if($this->_helper()->isKitProduct($item->getSku()))
//            {
//                if (!$item->getFlagWeightIsCalculated())
//                {
//                    $item->setWeight($this->_calculateKitWeightItem($item));
//                    $item->setFlagWeightIsCalculated(true);
//                }
//            }
//        }
//        return $items;
//        }
//        /**
//        * @return mixed
//        */
//        public function getAllVisibleItems(){
//        $items = parent::getAllVisibleItems();
//        foreach ($items as $item){
//            if($this->_helper()->isKitProduct($item->getSku())){
//                if (!$item->getFlagWeightIsCalculated()){
//                    $item->setWeight($this->_calculateKitWeightItem($item));
//                    $item->setFlagWeightIsCalculated(true);
//                }
//            }
//        }
//        return $items;
//        }
//        /**
//        * @param $item
//        * @return int
//        */
//        protected function _calculateKitWeightItem($item){
//        $weight = 0;
//        $product = $item->getProduct();
//        $optionIds = $item->getOptionByCode('option_ids');
//        if ($optionIds){
//            foreach (explode(',', $optionIds->getValue()) as $optionId) {
//                $option = $product->getOptionById($optionId);
//                if ($option AND 'products' == $option->getTitle()){
//                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
//                    $selectedProductIds = unserialize($itemOption->getValue());
//                    $selectedProduct = $this->_productCollection->create();
//                    foreach($selectedProductIds as $_productId){
//                        $selectedProduct->load($_productId);
//                        $weight += $selectedProduct->getWeight();
//                     }
//                  }
//                }
//             }
//          return $weight;
//        }
//        /**
//        * @return mixed
//        */
//        protected function _helper(){
//        return $this->helper('Udeytech\Productkit\Helper\Data');
//        }
}
