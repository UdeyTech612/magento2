<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Mineralmakeup;

use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Helper\Data;
use Udeytech\Productkit\Model\Source\Type;

/**
 * Class Index
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Index extends Action
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
     * @var ProductFactory
     */
    protected $_productFactory;
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
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
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
        ProductFactory $productFactory,
        Data $helper,
        \Udeytech\FreeSamplePage\Helper\Data $freeSampleHelper,
        Registry $registry,
        CategoryFactory $categoryFactory,
        \Magento\Customer\Model\Session $customerSession,
        Escaper $escaper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Session $checkoutSession,
        Cart $cart
    ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->_cart = $cart;
        $this->_freeSampleHelper = $freeSampleHelper;
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_productFactory = $productFactory;
        $this->_escaper = $escaper;
        $this->_categoryFactory = $categoryFactory;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }
    public function execute(){
        $product = $this->_productFactory->create();
        $expertKits = $this->helper('Udeytech\Productkit\Helper\Data')->getKitCollection($product);
        if($expertKits->count() == 0){
           throw new Exception(__('Expert kit not available'));
        }
        $this->_registry->register('expert_kits', $expertKits);
        $defaultKitId = $expertKits->getFirstItem()->getId();
        $this->_registry->register('current_expert_kit_id', $defaultKitId);
    }
}
