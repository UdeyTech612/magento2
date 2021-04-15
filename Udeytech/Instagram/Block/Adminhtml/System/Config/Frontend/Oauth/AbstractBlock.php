<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Phrase;
use Udeytech\Instagram\Helper\Config;

/**
 * Class AbstractBlock
 * @package Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth
 */
class AbstractBlock extends Template
{
    /**
     *
     * @var Config
     */
    protected $_configHelper;
    /**
     * @var
     */
    protected $_storeManager;


    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $configHelper,
        $data = []
    )
    {
        $this->_configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        return $this->getButton()->toHtml();
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        return parent::getContainerId();
    }


    /**
     * @return Phrase|string
     */
    public function getComment()
    {
        if ($this->_configHelper->isConnected()) {
            return '';
        }
        return __('Add <b>%1</b> to redirect urls for Instagram application',
            $this->_configHelper->getRedirectUrl());
    }
}
