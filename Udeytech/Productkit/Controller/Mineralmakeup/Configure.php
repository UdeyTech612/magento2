<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Productkit\Controller\Mineralmakeup;
use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Helper\Data;
/**
 * Class Configure
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Configure extends Action
{
    /**
     * @var
     */
    protected $_chackoutCart;
    /**
     * @var \Udeytech\FreeSamplePage\Helper\Data
     */
    protected $_freeSampleHelper;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var Escaper
     */
    protected $_escaper;
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * Configure constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param \Udeytech\FreeSamplePage\Helper\Data $freeSampleHelper
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Escaper $escaper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Session $checkoutSession
     * @param Cart $cart
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        \Udeytech\FreeSamplePage\Helper\Data $freeSampleHelper,
        Registry $registry,
        CategoryFactory $categoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        Escaper $escaper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Session $checkoutSession,
        Cart $cart ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->_cart = $cart;
        $this->_freeSampleHelper = $freeSampleHelper;
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_escaper = $escaper;
        $this->_categoryFactory = $categoryFactory;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $quoteItem = null;
        $cart = $this->_getCart();
        if ($id) {
            $quoteItem = $cart->getQuote()->getItemById($id);
        } else {
            $this->_getSession()->addError(__('NO Quote item id!'));
            $this->_goBack();
            return;
        }
        if (!$quoteItem) {
            $this->_getSession()->addError(__('Quote item is not found.'));
            $this->_goBack();
            return;
        }
        try {
            $type = $this->_helper->getKitNameBySku($quoteItem->getSku());
            $this->_registry->register('kit_quote_item', $quoteItem);
            $this->getRequest()->setParam('type', $type);
            if ($this->getRequest()->isPost()) {
                $this->_forward('addtocart');
            } else {
                $this->_forward('index');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError(__('Cannot configure product kit.'));
            throw new Exception($e);
            $this->_goBack();
            return;
        }
    }

    /**
     * @return mixed
     */
    protected function _getCart()
    {
        return $this->_checkoutCart->get();
    }

    /**
     * @return Session
     */
    protected function _getSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * @return $this
     */
    protected function _goBack()
    {
        $this->_redirect('checkout/cart');
        return $this;
    }
}
