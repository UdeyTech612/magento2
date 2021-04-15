<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Promo\Block\Adminhtml;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Notifications
 * @package Udeytech\Promo\Block\Adminhtml
 */
class Notifications extends Template
{
    /**
     * Notifications constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getSettingsUrl(){
        return $this->helper('Magento\Framework\Helper\Data')->getUrl("adminhtml/catalog_product_attribute/index", array());
    }

    /**
     * @return string
     */
    protected function _toHtml(){
        if (!$this->helper('Magento\Framework\Helper\Data')->isModuleEnabled('Udeytech_Rules')) {
            return parent::_toHtml();
        }
        return '';
    }
}

