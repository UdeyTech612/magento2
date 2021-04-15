<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\PromoBannersLite\Model\Source;
/**
 * Class Attributes
 * @package Udeytech\PromoBannersLite\Model\Source
 */
class Attributes implements \Magento\Framework\Option\ArrayInterface {
    /**
     *
     */
    const MODE_POPUP = 0;
    /**
     *
     */
    const MODE_INLINE = 1;

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
    }
