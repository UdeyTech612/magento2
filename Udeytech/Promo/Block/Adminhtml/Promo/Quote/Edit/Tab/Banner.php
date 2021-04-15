<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Udeytech\Promo\Block\Adminhtml\Promo\Quote\Edit\Tab;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Banner
 * @package Udeytech\Promo\Block\Adminhtml\Promo\Quote\Edit\Tab
 */
class Banner extends Template
{
    /**
     * Banner constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []){
        parent::__construct($context, $data);
    }
}

