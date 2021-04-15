<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Totalcustomization\Controller\Index;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Udeytech\Totalcustomization\Controller\Index
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
        if ($_REQUEST) {
            $multisel = $_REQUEST['multiproduct'];
            $multiselcount = explode(",", $multisel);
            try {
                foreach ($multiselcount as $productId) {
                    $params = array();
                    $params['qty'] = 1;
                    $_product = $this->productFactory->create()->load($productId);
                    if ($_product) {
                        $this->cart->addProduct($_product, $params);
                    }
                }
                /* keep this outside the loop */
                $this->cart->save();
                $this->messageManager->addSuccess(__('Add to cart successfully.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addException(
                    $e,
                    __('%1', $e->getMessage())
                );
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('error.'));
            }
        }
        return $this->resultPageFactory->create();
    }
}
