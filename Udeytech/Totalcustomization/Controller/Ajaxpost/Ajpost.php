<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Totalcustomization\Controller\Ajaxpost;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
/**
 * Class Ajpost
 * @package Udeytech\Totalcustomization\Controller\Ajaxpost
 */
class Ajpost extends Action {
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
     * Ajpost constructor.
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        try {
            $myJSON = preg_replace('/[^A-Za-z0-9,. -]/', '', $post['selected']);
            $posts = explode(',', $myJSON);
            foreach ($posts as $productId) {
                $params = array();
                $params['qty'] = $post['qty'];
                $_product = $this->productFactory->create()->load($productId);
                if ($_product) {
                    $this->cart->addProduct($_product, $params);
                }
            }
            $this->cart->save();
            $this->messageManager->addSuccess(__('Add to cart successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addException($e, __('%1', $e->getMessage()));
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('error.'));
        }
//return $this->resultPageFactory->create();
    }
}
