<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Source\Kit;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Makertype
 * @package Udeytech\Lookbook\Model\Source
 */
class Price extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('none'), 'value' => '0'],
                ['label' => __('Percent'), 'value' => '1'],
                ['label' => __('Fixed'), 'value' => '2']
            ];
        }
        return $this->_options;
    }
}
