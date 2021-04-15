<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Totalcustomization\Helper;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;

/**
 * Class Data
 * @package Udeytech\Totalcustomization\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    protected $_checkoutCart;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param Cart $checkoutCart
     * @param Session $checkoutSession
     * @param \Udeytech\Productkit\Helper\Data $helper
     * @param Image $imageHelper
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Cart $checkoutCart,
        Session $checkoutSession,
        \Udeytech\Productkit\Helper\Data $helper,
        Image $imageHelper,
        ProductFactory $productFactory
    )
    {
        $this->_checkoutCart = $checkoutCart;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->imageHelper = $imageHelper;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @param $id
     * @return string
     */
    public function getProductImageUrl($id)
    {
        try {
            $product = $this->productFactory->create()->load($id);
        } catch (NoSuchEntityException $e) {
            return 'Data not found';
        }
        $url = $this->imageHelper->init($product, 'product_base_image')->getUrl();
        return $url;
    }

    /**
     * @return bool
     */
    public function hasFreeKitInCart()
    {
        $objectManager = ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        //$cart = $this->_checkoutCart->create('checkout/cart');
        $quote = $cart->getQuote();
//        foreach ($quote->getAllVisibleItems() as $item) {
//            $product = $item->getProduct();
//            if (\Udeytech\Productkit\Helper\Data::FREE_KIT_SKU === $product->getSku()) {
//                return true;
//            }
//        }
        return false;
    }
}

