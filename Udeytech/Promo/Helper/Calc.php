<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Promo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Calc
 * @package Udeytech\Promo\Helper
 */
class Calc extends AbstractHelper
{
    /**
     * @var ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * Calc constructor.
     * @param Context $context
     * @param ScopeInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param $quote
     * @param $rule
     * @return int
     */
    function getQuoteSubtotal($quote, $rule)
    {
        $subtotal = 0;
        $storeScope = ScopeInterface::SCOPE_STORE;
        $taxInSubtotal = $this->scopeConfig->getValue('ampromo/general/tax_in_subtotal', $storeScope);
        $defualtCurrency = $this->scopeConfig->getValue('ampromo/general/default_currency', $storeScope);
        foreach ($quote->getItemsCollection() as $item) {
            if ($rule->getActions()->validate($item) && (!$item->getIsPromo() || $item->getPrice() != 0)) {
                if ($taxInSubtotal && $defualtCurrency)
                    $subtotal += $item->getBaseRowTotalInclTax();
                if ($taxInSubtotal && !$defualtCurrency)
                    $subtotal += $item->getRowTotalInclTax();
                if (!$taxInSubtotal && $defualtCurrency)
                    $subtotal += $item->getBaseRowTotal();
                if (!$taxInSubtotal && !$defualtCurrency)
                    $subtotal += $item->getRowTotal();
            }
        }
        return $subtotal;
    }
}

