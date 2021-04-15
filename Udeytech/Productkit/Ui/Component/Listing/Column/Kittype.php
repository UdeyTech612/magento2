<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Ui\Component\Listing\Column;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Kittype
 * @package Udeytech\Productkit\Ui\Component\Listing\Column
 */
class Kittype implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Free Kit')], ['value' => 1, 'label' => __('Custom Kit')]];
    }
}
