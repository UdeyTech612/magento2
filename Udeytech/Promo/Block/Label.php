<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Promo\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\SalesRule\Model\Rule;

/**
 * Class Label
 * @package Udeytech\Promo\Block
 */
class Label extends Template
{

    /**
     * Label constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getImage(Rule $validRule = null)
    {
        $validRule = $this->_getValidRule();

        return $this->helper("Udeytech\Promo\Helper\Image")->getLink($validRule->getData('ampromo_label_img'));
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getAlt(Rule $validRule = null)
    {
        $validRule = $this->_getValidRule();

        return $validRule->getData('ampromo_label_alt');
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getEnabled(Rule $validRule = null)
    {
        $validRule = $this->_getValidRule();
        return $validRule->getData('ampromo_label_enable');
    }
}

