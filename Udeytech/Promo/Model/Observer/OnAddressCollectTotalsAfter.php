<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OnAddressCollectTotalsAfter
 * @package Udeytech\Promo\Model\Observer
 */
class OnAddressCollectTotalsAfter implements ObserverInterface
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
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;
    /**
     * OnAddressCollectTotalsAfter constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
    ){
        $this->logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollection = $productCollection;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        if ($this->_selfExecuted) {
            return true;
        }
        $quote = $observer->getQuoteAddress()->getQuote();
        $items = $quote->getAllItems();
        $realCount = 0;
        foreach ($items as $item) {
            if ($item->getIsPromo()) {
                $item->isDeleted(false);
                $this->resetWeee($item);
                } else {
                $realCount++;
            }
        }
        if ($realCount == 0) {
            $this->_selfExecuted = true;
            foreach ($items as $item) {
                $itemId = $item->getItemId();
                $quote->removeItem($itemId)->save();
            }
        }
        if ($this->_scopeConfig->isSetFlag('ampromo/general/auto_add')) {
            $toAdd = $this->_registry->registry('ampromo/registry')->getPromoItems();
            if (is_array($toAdd)) {
                unset($toAdd['_groups']);
                foreach ($items as $item) {
                    $sku = $item->getProduct()->getData('sku');
                    if (!isset($toAdd[$sku])) {
                        continue;
                    }
                    //$qtyIncreased = isset($toAdd[$sku]['qtyIncreased']) ? $toAdd[$sku]['qtyIncreased'] : false;
                    /* weak code - for avoid issue with added to cart automatically */
                    $qtyIncreased = true;
                    if ($item->getIsPromo()) {
                        if (!$qtyIncreased) {
                            unset($toAdd[$sku]); // to allow to decrease qty
                        } else {
                            $toAdd[$sku]['qty'] -= $item->getQty();
                        }
                    }
                }
                $deleted = array();
                if ($observer->getQuoteAddress()->getAddressType() === 'shipping') {
                    $rulesIds = explode(',', $quote->getAppliedRuleIds());
                    $deleted = $this->_registry->registry('ampromo/registry')->getDeletedItems($rulesIds);
                }
                $this->_toAdd = array();
                foreach ($toAdd as $sku => $item) {
                    if ($item['qty'] > 0 && $item['auto_add'] && !isset($deleted[$sku])) {
                        $product = $this->_productCollection->create()->loadByAttribute('sku', $sku);
                        if (isset($this->_toAdd[$product->getId()])) {
                            $this->_toAdd[$product->getId()]['qty'] += $item['qty'];
                        } else {
                            $this->_toAdd[$product->getId()] = array(
                                'product' => $product,
                                'qty' => $item['qty']
                            );
                        }
                    }
                }
            }
        }
    }
}
