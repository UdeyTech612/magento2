<?php

/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Promo\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\Promo\Model\Source\DisplayMode;

/**
 * Class Add
 * @package Udeytech\Promo\Block
 */
class Add extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Add constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|string
     */
    protected function _toHtml()
    {
        if ($this->_scopeConfig->getValue('ampromo/popup/mode') == DisplayMode::MODE_INLINE) {
            return false;
        }
        return parent::_toHtml();
    }
}

