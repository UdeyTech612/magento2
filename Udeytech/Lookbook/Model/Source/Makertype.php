<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Lookbook\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Makertype
 * @package Udeytech\Lookbook\Model\Source
 */
class Makertype implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = array(
            array('value' => 'dynamic', 'label' => __('Dynamic')),
            array('value' => 'static', 'label' => __('Static'))
        );
        return $this->_options;
    }
}

