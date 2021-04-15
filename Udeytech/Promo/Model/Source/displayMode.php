<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Source;
/**
 * Class DisplayMode
 * @package Udeytech\Promo\Model\Source
 */
class DisplayMode implements \Magento\Framework\Option\ArrayInterface {
    /**
     * @var
     */
    const MODE_POPUP = 0;
    /**
     * @var
     */
    const MODE_INLINE = 1;

    /**
     * @return array
     */
    public function toOptionArray() {
        return [
            ['value' => self::MODE_POPUP,'label' => __('Popup')],
            ['value' => self::MODE_INLINE,'label' => __('Inside Page')]
         ];
     }
  }
