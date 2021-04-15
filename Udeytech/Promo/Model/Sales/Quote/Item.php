<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
use Udeytech\Promo\Model\Sales\Quote;

/**
 * Class Item
 */
class Item  {
    /**
     * @var null
     */
    protected $_ruleId = null;
    /**
     * @var null
     */
    protected $_rule = null;
    /**
     * @var
     */
    protected $_sourcePrice;
    /**
     * @var
     */
    protected $_taxConfig;
    /**
     * @var \Magento\Tax\Model\TaxCalculation
     */
    protected $_taxCalculation;
    /**
     * Item constructor.
     * @param \Magento\Tax\Model\TaxCalculation $taxCalculation
     * @param \Magento\Framework\ObjectManagerInterface $_objectManager
     */
    public function __construct(
        \Magento\Tax\Model\TaxCalculation $taxCalculation,
        \Magento\Framework\ObjectManagerInterface $_objectManager
    ){
    $this->_objectManager = $_objectManager;
    $this->_taxCalculation = $taxCalculation;
   }
}

