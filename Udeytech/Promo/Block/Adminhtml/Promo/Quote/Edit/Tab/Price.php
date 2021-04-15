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
 * Class Price
 * @package Udeytech\Promo\Block\Adminhtml\Promo\Quote\Edit\Tab
 */
class Price extends Template
{
    /**
     * Price constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }
}

