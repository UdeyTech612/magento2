<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth;


use Magento\Backend\Block\Template\Context;
use Udeytech\Instagram\Helper\Config;
use Udeytech\Instagram\Model\Instagram\Api;

/**
 * Class Connect
 * @package Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth
 */
class Connect extends AbstractBlock
{
    /**
     * @var null
     */
    protected $_api = null;

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * Connect constructor.
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
        parent::__construct($context, $configHelper, $data);
        $this->jsLayout = isset($data['jsLayout'])
        && is_array($data['jsLayout'])
            ? $data['jsLayout'] : [];
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    public function getButton()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setLabel(__('Connect'))
            ->setStyle("width:280px")
            ->setId('udeytech_instagram_oauth');
        return $button;
    }


    /**
     * @return mixed
     */
    public function getLoginUrl()
    {
        return $this->_getApi()->getLoginUrl(array('basic', 'public_content'));
    }

    /**
     * @return Udeytech_Instagram_Model_Instagram_Api
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = new Api([
                'apiKey' => $this->_configHelper->getClientId(),
                'apiSecret' => $this->_configHelper->getClientSecret(),
                'apiCallback' => $this->_configHelper->getRedirectUrl()]);
        }
        return $this->_api;
    }

    /**
     * @return string
     */
    public function getSaveConfigUrl()
    {
        return $this->getUrl("udeytech_instagram/api/save");
    }

}
