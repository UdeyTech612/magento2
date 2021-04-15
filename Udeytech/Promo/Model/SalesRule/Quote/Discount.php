<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udedytech\Promo\Model\SalesRule\Quote;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Quote\Address;
use Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory;
use Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory;
use Magento\SalesRule\Model\Validator;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Discount
 * @package Udedytech\Promo\Model\SalesRule\Quote
 */
class Discount extends \Magento\SalesRule\Model\Quote\Discount
{
    /**
     * @var ScopeInterface
     */
    protected $_storeConfig;

    /**
     * Discount constructor.
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Validator $validator
     * @param ScopeInterface $storeConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param RuleDiscountInterfaceFactory|null $discountInterfaceFactory
     * @param DiscountDataInterfaceFactory|null $discountDataInterfaceFactory
     */
    public function __construct(
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Validator $validator,
        ScopeInterface $storeConfig,
        PriceCurrencyInterface $priceCurrency,
        RuleDiscountInterfaceFactory $discountInterfaceFactory = null,
        DiscountDataInterfaceFactory $discountDataInterfaceFactory = null
    ){
        $this->_storeConfig = $storeConfig;
        parent::__construct($eventManager, $storeManager, $validator, $priceCurrency, $discountInterfaceFactory, $discountDataInterfaceFactory);
    }
//    public function fetch(Address $address)
//    {
//        if (!$this->_storeConfig->getValue('amrules/breakdown_settings/breakdown')) {
//            return parent::fetch($address);
//        }
//        $amount = $address->getDiscountAmount();
//        if ($amount != 0) {
//            $address->addTotal(array(
//                'code' => $this->getCode(),
//                'title' => Mage::helper('sales')->__('Discount'),
//                'value' => $amount,
//                'full_info' => $address->getFullDescr(),
//            ));
//        }
//        return $this;
//    }
}

