<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Lookbook\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Imageposition
 * @package Udeytech\Lookbook\Model\Source
 */
class Imageposition implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = array(
            array('value' => 'top', 'label' => ('Top (Horizontal Image)')),
            array('value' => 'left', 'label' => ('Left (Vertical Image)'))
        );
        return $this->_options;
    }
}

