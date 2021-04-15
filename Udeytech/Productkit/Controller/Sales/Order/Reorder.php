<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Udeytech\Productkit\Controller\Mineralmakeup;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\FreeSamplePage\Helper\Data;
/**
 * Class Free
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Free extends Action {
    /**
     * @var Data
     */
    protected $_freeSampleHelper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * Free constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $freeSampleHelper
     */
    protected $_registry;
    protected $_checkoutSession;
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        Data $freeSampleHelper
    ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->_freeSampleHelper = $freeSampleHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    public function execute(){
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = $this->_registry->registry('current_order');
        $cart = $this->_checkoutSession;
        $cartTruncated = false;
           $items = $order->getItemsCollection();
        $kitHelper = $this->helper('Udeytech\Productkit\Helper\Data');
        foreach ($items as $item){
            if ($kitHelper->isKitProduct($item)) {
                $url = $item->getSku() === \Udeytech\Productkit\Helper\Data::FREE_KIT_SKU ? $kitHelper->getWizardUrlFree() : $kitHelper->getWizardUrlCustom();
                $message = $this->__('Unfortunately "%s" has not been added to your basket. Please visit the <a href="%s">following page</a> to configure this product.', $item->getName(), $url);
                $this->_checkoutSession->addNotice($message);
                continue;
            }
            try {
                $cart->addOrderItem($item);
            } catch (\Exception $e){
                if($this->_checkoutSession->getUseNotice(true)){
                    $this->_checkoutSession->addNotice($e->getMessage());
                } else {
                    $this->_checkoutSession->addError($e->getMessage());
                }
                $this->_redirect('*/*/history');
            } catch (\Exception $e) {
                $this->_checkoutSession->addException($e, __('Cannot add the item to shopping cart.'));
                $this->_redirect('checkout/cart');
            }
        }
        $cart->save();
        $this->_redirect('checkout/cart');
    }
}
