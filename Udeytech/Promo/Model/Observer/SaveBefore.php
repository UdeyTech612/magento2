<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
/**
 * Class SaveBefore
 * @package Udeytech\Promo\Model\Observer
 */
class SaveBefore implements ObserverInterface {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * SaveBefore constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
      * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $controllerAction = $observer->getRule()->getData();
        if ($controllerAction['simple_action'] == 'ampromo_cart') {
            $data = $observer->getRule()->getData();
            $r = array('type' => 'salesrule/rule_condition_product_combine',
                'attribute' => null, 'operator' => null, 'value' => '1', 'is_value_processed' => null, 'aggregator' => 'any',
                'conditions' => array(),);
            $data['actions_serialized'] = serialize($r);
            $observer->getRule()->setData($data);
        }
    }
}
