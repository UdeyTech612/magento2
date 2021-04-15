<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Framework\Model\Context;
/**
 * Class SalesMysql4Quote
 * @package Udeytech\Productkit\Model\Rewrite
 */
class SalesMysql4Quote {
    /**
     * SalesMysql4Quote constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        $this->_registry = $registry;
        parent::_construct($context, $data);
    }
    /**
     *
     */
    public function markQuotesRecollectOnCatalogRules()
    {
        $qs = "SELECT COUNT(DISTINCT product_id) FROM {$this->getTable('catalogrule/rule_product_price')}";
        $cnt = $this->_getReadAdapter()->fetchOne($qs);
        if ($cnt) {
            parent::markQuotesRecollectOnCatalogRules();
        }
        return;
    }
}
