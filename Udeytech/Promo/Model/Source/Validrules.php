<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Source;

/**
 * Class Validrules
 * @package Udeytech\Promo\Model\Source
 */
class Validrules implements \Magento\Framework\Option\ArrayInterface {
    /**
     * @return array
     */
    public function toOptionArray() {
        $options = array(
            0 => __('Current Product Only'),
            1 => __('Whole Shopping Cart Content'));
        return $options;
    }
}
