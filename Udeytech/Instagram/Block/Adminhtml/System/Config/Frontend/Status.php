<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Udeytech\Instagram\Helper\Config;

/**
 * Class Status
 * @package Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend
 */
class Status extends Field
{
    /**
     *
     * @var Config
     */
    protected $_configHelper;

    /**
     * Status constructor.
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
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setBold(true);

        if ($this->_configHelper->isConnected()) {
            $element->setValue(__('Connected to Instagram'));
            $element->addClass('instagram_status')->addClass('success');
        } else {
            $element->setValue(__('Not connected to Instagram'));
            $element->addClass('instagram_status')->addClass('error');
        }
        return '<p id="' . $element->getHtmlId() . '_label" '
            . $element->serialize($element->getHtmlAttributes()) . '>'
            . parent::_getElementHtml($element) . '</p><input id="'
            . $element->getHtmlId() . '" value="' . (int)$this
                ->_configHelper->isConnected() . '" type="hidden"/>';
    }

    /**
     * @return Field
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        /* @var $head Mage_Page_Block_Html_Head */
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addItem('skin_css', 'Udeytech_Instagram::css/styles.css');
        }
        return parent::_prepareLayout();
    }
}
