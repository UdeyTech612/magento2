<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Framework\Model\Context;
use Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory;
/**
 * Class SalesQuoteItem
 * @package Udeytech\Productkit\Model\Rewrite
 */
class SalesQuoteItem extends \Magento\Quote\Model\Quote\Item {
    /**
     * @var
     */
    protected $_expertCollection;
    /**
     * SalesQuoteItem constructor.
     * @param Context $context
     * @param CollectionFactory $expertCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $expertCollection,
        array $data = []
    ){
        $this->_registry = $registry;
        $this->_expertCollection = $expertCollection;
        parent::_construct($context, $data);
    }
    /**
     * @return mixed
     */
    public function getName(){
        if ($this->getKitChoose()) {
            return $this->getKitChoose()->getKitChooseTitle();
            } else {
            return $this->getData('name');
        }
    }
    /**
     * @return bool
     */
    public function getKitChoose()
    {
        if(!$this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($this->getProduct()->getSku())) {
            return false;
         }
         $kitChoose = $this->_expertCollection->create()->load($this->getProductkitChooseId());
         if (!$kitChoose->getId())
            $kitChoose = false;
        return $kitChoose;
    }
    /**
     * @return bool
     */
    public function isItemOptionsHasPrice()
    {
        if ($this->getKitChoose())
            return !((bool)$this->getKitChoose()->getPrice());
        return true;
    }
}
