<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement as AbstractFormElement;
use Magento\Framework\Exception\LocalizedException;
use Udeytech\Instagram\Helper\Config;

/**
 * Class Oauth
 * @package Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend
 */
class Oauth extends Field
{
    /**
     *
     * @var Config
     */
    protected $_configHelper;


    /**
     * Oauth constructor.
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
     * @param AbstractFormElement $element
     * @return string
     */
    public function render(AbstractFormElement $element)
    {
        $element->setScope(false);
        $element->setCanUseWebsiteValue(false);
        $element->setCanUseDefaultValue(false);
        return parent::render($element);
    }

    /**
     * @param AbstractFormElement $element
     * @return string
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractFormElement $element)
    {
        $confClassPart = 'Udeytech\Instagram\Block\Adminhtml\System\Config';
        if (!$this->_configHelper->isConnected()) {
            $button = $this->getLayout()->createBlock(
                $confClassPart . '\Frontend\Oauth\Connect'
                , 'udeytech_instagram_oauth');
            $button->setTemplate('system/config/oauth/connect.phtml');
        } else {
            $button = $this->getLayout()->createBlock(
                $confClassPart . '\Frontend\Oauth\Disconnect'
                , 'udeytech_instagram_oauth');
            $button->setTemplate('system/config/oauth/disconnect.phtml');
        }
        $button->setContainerId($element->getContainer()->getHtmlId());
        return $button->toHtml();
    }

}
