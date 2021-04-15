<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Source\Banner;

/**
 * Class Mode
 * @package Udeytech\Promo\Model\Source\Banner
 */
class Mode implements \Magento\Framework\Option\ArrayInterface {
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
    public function toOptionArray(){
        return array(
            array('value' => \Udeytech\Promo\Block\Banner::MODE_PRODUCT,'label' => __('Product')),
            array('value' => \Udeytech\Promo\Block\Banner::MODE_CART, 'label' => __('Cart')));
    }
}
