<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Mineralmakeup;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Helper\Data;

/**
 * Class Isfreekitduplicate
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Isfreekitduplicate extends Action
{
    /**
     * @var
     */
    protected $_chackoutCart;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * Isfreekitduplicate constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Session $checkoutSession
     * @param Cart $cart
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Session $checkoutSession,
        Cart $cart
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_cart = $cart;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $isAjax = $this->getRequest()->getParam('isAjax', false);
        if ($isAjax) {
            $response['isfreekitduplicate'] = $this->_isFreeKitDuplicate();
            $response = $this->_jsonHelper->jsonEncode($response);
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * @return bool
     */
    protected function _isFreeKitDuplicate()
    {

        $quote = $this->getQuotes();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getSku() === Data::FREE_KIT_SKU) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuotes()
    {
        return $this->_checkoutSession->getQuote();
    }
}
