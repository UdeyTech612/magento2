<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Promo\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Gift
 * @package Udeytech\Promo\Block
 */
class Gift extends Template
{
    /**
     * Gift constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }
}

