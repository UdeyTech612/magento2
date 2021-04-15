<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OnQuoteRemoveItem
 * @package Udeytech\Promo\Model\Observer
 */
class OnQuoteRemoveItem implements ObserverInterface {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * OnQuoteRemoveItem constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry ){
        $this->logger = $logger;
        $this->_request = $request;
        $this->_registry = $registry;
       }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
            $id = (int)$this->_request->getRequest()->getParam('id');
            $item = $observer->getEvent()->getQuoteItem();
            $registry = $this->_registry->registry('ampromo/registry');
        if (($item->getId()==$id) && $item->getIsPromo() && !$item->getParentId()){
            $sku = $item->getProduct()->getData('sku');
            $registry->deleteProduct($sku);
            } else {
            $rulesIds = explode(',', $item->getQuote()->getAppliedRuleIds());
            $registry->checkDeletedItems($rulesIds);
        }
    }
}
