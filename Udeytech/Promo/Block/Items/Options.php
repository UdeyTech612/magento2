<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Udeytech\Promo\Block\Items;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Options
 * @package Udeytech\Promo\Block\Items
 */
class Options extends Template{
    /**
     * Options constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []){
        parent::__construct($context, $data);
    }
}

