<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSampleQuiz\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Options
 * @package Udeytech\FreeSampleQuiz\Model\Config\Source
 */
class Options implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Cool',
                'value' => 'Cool'
            ],
            1 => [
                'label' => 'Neutral',
                'value' => 'Neutral'
            ],
            2 => [
                'label' => 'Warm',
                'value' => 'Warm'
            ],
            3 => [
                'label' => 'Fair',
                'value' => 'Fair'
            ],
            4 => [
                'label' => 'Light',
                'value' => 'Light'
            ],
            5 => [
                'label' => 'Medium',
                'value' => 'Medium'
            ],
            6 => [
                'label' => 'Tan',
                'value' => 'Tan'
            ],
        ];
        return $options;
    }
}

?>
