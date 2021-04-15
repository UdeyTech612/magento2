<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OnCollectTotalsBefore
 * @package Udeytech\Promo\Model\Observer
 */
class OnCollectTotalsBefore implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OnCollectTotalsBefore constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){

    }
}

