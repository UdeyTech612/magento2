<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SalesRulePrepareSave
 * @package Udeytech\Promo\Model\Observer
 */

class SalesRulePrepareSave implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * SalesRulePrepareSave constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_top_banner_img');
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_after_name_banner_img');
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_label_img');
    }

    /**
     * @param $request
     * @param $file
     */
    protected function _savePromoRuleImage($request, $file){
        if($data = $request->getPost()){
            if(isset($data[$file]) && isset($data[$file]['delete'])){
                $data[$file] = null;
            } else {
                if(isset($_FILES[$file]['name']) && $_FILES[$file]['name'] != ''){
                    $fileName = $this->helper("Udeytech\Promo\Helper\Image")->upload($file);
                    $data[$file] = $fileName;
                } else {
                    if(isset($data[$file]['value']))
                        $data[$file] = basename($data[$file]['value']);
                }
            }
            $request->setPost($data);
        }
    }
}
