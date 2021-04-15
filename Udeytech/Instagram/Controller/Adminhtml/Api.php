<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Adminhtml;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Instagram\Helper\Config;

/**
 * Class Api
 * @package Udeytech\Instagram\Controller\Adminhtml
 */
abstract class Api extends Action
{
    /**
     * @var null
     */
    protected $_api = null;
    /**
     * @var Config
     */
    protected $_configHelper;
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Api constructor.
     * @param Context $context
     * @param Config $configHelper
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Config $configHelper,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
    }


    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl("udeytech_instagram/api/connect");
    }

    /**
     * @return Udeytech_Instagram_Model_Instagram_Api
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = new \Udeytech\Instagram\Model\Instagram\Api([
                'apiKey' => $this->_configHelper->getClientId(),
                'apiSecret' => $this->_configHelper->getClientSecret(),
                'apiCallback' => $this->_configHelper->getRedirectUrl()]);
        }
        return $this->_api;
    }
}
