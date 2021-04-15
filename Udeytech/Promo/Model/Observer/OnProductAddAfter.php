<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OnProductAddAfter
 * @package Udeytech\Promo\Model\Observer
 */
class OnProductAddAfter implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * OnProductAddAfter constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\ObjectManagerInterface $_objectManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
){
        $this->logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $_objectManager;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $items = $observer->getItems();
        $this->_setItemPrefix($items);
        foreach ($items as $item) {
            if ($item->getIsPromo())
                $item->setNoDiscount(true);
        }
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
            } elseif (isset($buyRequest['ampromo_rule_id']) && !isset($labelName)){
                $rule = $this->_loadRule($buyRequest['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
                $item->setLabelName(1);
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function _loadRule($id){
        if (!isset($this->_rules[$id])) {
            $this->_rules[$id] = $this->_objectManager->create('Magento\SalesRule\Model\Rule')->load($id);
        }
        return $this->_rules[$id];
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
