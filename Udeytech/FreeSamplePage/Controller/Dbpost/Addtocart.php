<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSamplePage\Controller\Dbpost;

use Exception;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Addtocart
 * @package Udeytech\FreeSamplePage\Controller\Dbpost
 */
class Addtocart extends Action
{
    /**
     * @param Context $context
     * @param FormKey $formKey
     * @param JsonFactory $resultJsonFactory
     * @param Cart $cart
     * @param ProductFactory $productFactory
     */
    protected $_storeManager;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Addtocart constructor.
     * @param Context $context
     * @param FormKey $formKey
     * @param JsonFactory $resultJsonFactory
     * @param Cart $cart
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        JsonFactory $resultJsonFactory,
        Cart $cart,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->_storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
    }

    /**
     * @return ResultJson
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultData = ['status' => 'success', 'status_code' => 1, 'message' => __('Products Added successfully.')];
        $result = $this->resultJsonFactory->create();
        $qty = 1;
        $productId = $data['product'];
        $product = $this->productFactory->create()->load($productId);
        $params = [
            'product' => $productId,
            'bundle_option' => $data['bundle_option'],
            'qty' => $qty
        ];
        try {

            /**
             * Add bundle product in cart
             */

            if ($product->getId()) {
                $this->cart->addProduct($product, $params);
                $this->cart->save();
            }
        } catch (\Exception $e) {
            $resultData = [
                'status' => 'fail',
                'status_code' => 0,
                'message' => __('Unable to add the Product. Exception ' . $e->getMessage())
            ];
            return $result->setData($resultData);
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $url = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . 'checkout/cart';
        $resultRedirect->setUrl($url);
        return $resultRedirect;
    }
}
