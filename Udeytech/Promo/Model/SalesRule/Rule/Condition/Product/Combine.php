<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Promo\Model\SalesRule\Rule\Condition\Product;
use Magento\Framework\DataObject;
use Magento\Rule\Model\Condition\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Combine
 * @package Udeytech\Promo\Model\SalesRule\Rule\Condition\Product
 */
class Combine {
    /**
     * @var
     */
    protected $storeData;

    /**
     * Combine constructor.
     * @param Context $context
     * @param ScopeInterface $storeData
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeInterface $storeData,
        array $data = []){
        $this->_storeData = $storeData;
        parent::__construct($context, $data);
    }
}
