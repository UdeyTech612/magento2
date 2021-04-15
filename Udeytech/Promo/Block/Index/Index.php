<?php
declare(strict_types=1);
namespace Udeytech\Promo\Block\Index;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
/**
 * Class Index
 * @package Udeytech\Promo\Block\Index
 */
class Index extends Template {
    /**
     * Index constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }
}

