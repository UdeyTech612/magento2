<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Source;
/**
 * Class Attributes
 * @package Udeytech\Promo\Model\Source
 */
class Attributes implements \Magento\Framework\Option\ArrayInterface {
    /**
     * @return array
     */
    public function toOptionArray(){
        $options = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coll = $objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::class);
        $coll->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4)->setFrontendInputTypeFilter(array('text','textarea'));
        $attrAll = $coll->load()->getItems();
        foreach ($attrAll as $attribute){
            $label = $attribute->getFrontendLabel();
            if($label){
                $options[] = array('value' => $attribute->getFrontendLabel(),'label' => $label);
             }
         }
        return $options;
     }

    /**
     * var Array
     */
    public function toArray(){
        $arr = array(array('' => '-'));
        $optionArray = $this->toOptionArray();
        foreach ($optionArray as $option) {
            $arr[$option['value']] = $option['label'];
        }
    }
}
