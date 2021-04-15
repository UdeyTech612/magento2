<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OnOrderPlaceBefore
 * @package Udeytech\Promo\Model\Observer
 */
class OnOrderPlaceBefore implements ObserverInterface {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * OnOrderPlaceBefore constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            LoggerInterface $logger,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig){
            $this->logger = $logger;
            $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $order = $observer->getOrder();
        $this->_setItemPrefix($order->getAllItems());
    }

    /**
     * @param $items
     */
    protected function _setItemPrefix($items){
        $prefix = $this->_scopeConfig->getValue('ampromo/general/prefix');
        foreach ($items as $item) {
            $buyRequest = $item->getBuyRequest();
            $labelName  = $item->getLabelName();
            if (isset($buyRequest['options']['ampromo_rule_id'])) {
                $rule = $this->_loadRule($buyRequest['options']['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
            } elseif (isset($buyRequest['ampromo_rule_id']) && !isset($labelName)
            ){
                $rule = $this->_loadRule($buyRequest['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
                $item->setLabelName(1);
            }
        }
    }

    /**
     * @param $rule
     * @param $item
     * @param $prefix
     */
    protected function _generateNameWithLabel($rule, $item, $prefix){
        $ruleLabel = $rule->getAmpromoPrefix();
        $rulePrefix = !empty($ruleLabel) ? $ruleLabel : $prefix;
        $item->setName($rulePrefix . ' ' . $item->getName());
    }
}
