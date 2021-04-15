<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Promo\Block\Items;
use Magento\Framework\View\Element\Template;
/**
 * Class Bundle
 * @package Udeytech\Promo\Block\Items
 */
class Bundle extends Template
{
    /**
     * Bundle constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = []){
       parent::__construct($context, $data);
    }
    /**
     * @param $option
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionHtml($option){
        if (!isset($this->_optionRenderers[$option->getType()])){
            return __("There is no defined renderer for <b>%s</b> option type.", $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])->setProduct($this->getProduct())->setOption($option)->toHtml();
    }
}

