<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSamplePage\Controller\Index;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Udeytech\FreeSamplePage\Controller\Index
 */
class Index extends Action
{
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var Product
     */
    protected $productFactory;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
     * @param Cart $cart
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductFactory $productFactory,
        Cart $cart
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
