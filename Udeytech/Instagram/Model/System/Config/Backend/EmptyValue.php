<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Class EmptyValue
 * @package Udeytech\Instagram\Model\System\Config\Backend
 */
class EmptyValue extends Value
{
    /**
     * @return string|null
     */
    public function getValue()
    {
        return null;
    }
}
