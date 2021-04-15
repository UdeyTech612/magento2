<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Promo\Block;

use Magento\Catalog\Model\Product\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Model\CartFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Banner
 * @package Udeytech\Promo\Block
 */
class Banner extends Template
{

    /**
     *
     */
    const MODE_PRODUCT = 'product';
    /**
     *
     */
    const MODE_CART = 'cart';
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var
     */
    protected $_validRules;
    /**
     * @var
     */
    protected $_rulesCollection;
    /**
     * @var CartFactory
     */
    protected $_cartFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var
     */
    protected $productCollectionFactory;

    /**
     * Banner constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param CartFactory $cartFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        CartFactory $cartFactory,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = [])
    {
        $this->_request = $request;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_cartFactory = $cartFactory;
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getDescription(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        return $validRule->getData('ampromo_' . $this->getPosition() . '_banner_description');
    }

    /**
     * @return DataObject|mixed
     */
    protected function _getValidRule()
    {
        $validRule = new DataObject();
        $validRules = $this->_getValidRules();
        if (count($validRules) > 0 && array_key_exists(0, $validRules)) {
            $validRule = $validRules[0];
        }
        return $validRule;
    }

    /**
     * @return DataObject
     */
    protected function _getValidRules()
    {
        if ($this->_validRules === null) {
            $this->_validRules = array();
            if ($this->_scopeConfig->getValues('ampromo/banners/mode') === self::MODE_PRODUCT) {
                $this->_validRules = $this->_getProductBasedValidRule();
            } else if ($this->_scopeConfig->getValues('ampromo/banners/mode') === self::MODE_CART) {
                $this->_validRules = $this->_getQuoteBasedValidRule();
            } else {
                $this->_validRules = new DataObject();
            }
        }
        return $this->_validRules;
    }

    /**
     * @return array|DataObject
     */
    public function getValidRules()
    {
        $validRules = $this->_getValidRules();
        if ($this->_scopeConfig->getValue('ampromo/banners/single') === '1' && count($validRules) > 0) {
            return array_slice($validRules, 0, 1);
        }
        return $validRules;
    }

    /**
     * @return mixed
     */
    protected function _getProductBasedValidRule()
    {
        $currentQuote = $this->_cartFactory->get()->getQuote();
        $quoteItem = new DataObject();
        $quoteItem->setProduct($this->getProduct());
        $quoteItem->setStoreId($this->_cartFactory->get()->getQuote()->getStoreId());
        $quoteItem->setIsVirtual(false);
        $quoteItem->setQuote($currentQuote);
        $quoteItem->setAllItems(array($this->getProduct()));
        foreach ($this->_getRulesCollection() as $rule) {
            if ($rule->getActions()->validate($quoteItem)) {
                $this->_validRules[] = $rule;
            }
        }
        return $this->_validRules;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getRulesCollection()
    {
        if (!$this->_rulesCollection) {
            $quote = $this->_cartFactory->get()->getQuote();
            $store = $this->_storeManager->getStore($quote->getStoreId());
            $this->_rulesCollection = $this->_collectionFactory->getCollection()
                ->setValidationFilter($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
            $this->_rulesCollection->getSelect()->where("simple_action in ('ampromo_items', 'ampromo_product') and is_active = 1");
            if (!$this->_request->isSingleStoreMode()) {
                $this->_rulesCollection->getSelect()->where("FIND_IN_SET ('{$store->getId()}', amstore_ids) or amstore_ids = '' or FIND_IN_SET (0, amstore_ids)");
            }
        }
        return $this->_rulesCollection;
    }

    /**
     * @return mixed
     */
    protected function _getQuoteBasedValidRule()
    {
        if (!$this->helper('Udeytech\Promo\Helper\Data')->checkAvailableQty($this->getProduct(), $this->getProduct()->getStockItem()->getMinSaleQty())) {
            $this->_validRules;
        }
        $product = $this->getProduct();
        if ($product->getTypeId() === 'configurable') {
            $childrenProducts = $product->getChildrenProducts();
            foreach ($childrenProducts as $key => $childProduct) {
                if (!$this->helper('Udeytech\Promo\Helper\Data')->checkAvailableQty($childProduct, 1)) {
                    unset($childrenProducts[$key]);
                }
            }
            if (count($childrenProducts) > 0) {
                $product = end($childrenProducts);
                $product = $this->_productCollectionFactory->create()->load($product->getId());
            }
        }

        $currentQuote = Mage::getModel('checkout/cart')->getQuote();
        $afterQuote = Mage::getModel('sales/quote');
        $afterQuote->addProduct($this->getProduct(), new DataObject(array('qty' => $this->getProduct()->getStockItem()->getMinSaleQty())));
        $afterQuote->merge($currentQuote);
        $afterQuote->setIsFake(true);
        $afterQuote->collectTotals();
        $currentRuleIds = $currentQuote->getAppliedRuleIds();
        $afterRuleIds = $afterQuote->getAppliedRuleIds();
        $afterRulesArray = explode(",", $afterRuleIds);
        $currentRulesArray = explode(",", $currentRuleIds);
        foreach ($afterRulesArray as $ruleId) {
            if (!in_array($ruleId, $currentRulesArray)) {
                $this->_validRules[] = Mage::getModel('salesrule/rule')->load($ruleId);
            }
        }
        return $this->_validRules;
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getImage(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        return $this->helper("ampromo/image")->getLink($validRule->getData('ampromo_' . $this->getPosition() . '_banner_img'));
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getAlt(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        return $validRule->getData('ampromo_' . $this->getPosition() . '_banner_alt');
    }

    /**
     * @param Rule|null $validRule
     * @return mixed
     */
    function getHoverText(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        return $validRule->getData('ampromo_' . $this->getPosition() . '_banner_hover_text');
    }

    /**
     * @param Rule|null $validRule
     * @return mixed|string
     */
    function getLink(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        return $validRule->getData('ampromo_' . $this->getPosition() . '_banner_link') ? $validRule->getData('ampromo_' . $this->getPosition() . '_banner_link') : "#";
    }

    /**
     * @param Rule|null $validRule
     * @return bool
     */
    function isShowGiftImages(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }

        return $validRule->getData('ampromo_' . $this->getPosition() . '_banner_gift_images') == 1;
    }

    /**
     * @param Rule|null $validRule
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection\AbstractDb
     */
    function getProducts(Rule $validRule = null)
    {
        if ($validRule === null) {
            $validRule = $this->_getValidRule();
        }
        $products = array();
        if ('ampromo_product' == $validRule->getSimpleAction())
            return $products;
        $promoSku = $validRule->getPromoSku();
        $skuArray = explode(",", $promoSku);
        array_walk($skuArray, array($this, 'trimValue'));
        if (!empty($promoSku)) {
            $products = $this->_productCollectionFactory->create()
                ->addFieldToFilter('sku', array('in' => $skuArray))->addUrlRewrite()->addFinalPrice()
                ->addAttributeToSelect(array('name', 'thumbnail', $this->getAttributeHeader(), $this->getAttributeDescription()))
                ->addAttributeToFilter('status', array('eq' => Status::STATUS_ENABLED));
            $products = $this->applyPromoQty($products, $skuArray);
        }
        return $products;
    }

    /**
     * @return mixed
     */
    function getAttributeHeader()
    {
        return $this->_scopeConfig->getValue('ampromo/gift_images/attribute_header');
    }

    /**
     * @return mixed
     */
    function getAttributeDescription()
    {
        return $this->_scopeConfig->getValue('ampromo/gift_images/attribute_description');
    }

    /**
     * @param $products
     * @param $skuArray
     * @return mixed
     */
    function applyPromoQty($products, $skuArray)
    {
        $promoSkuCounts = array_count_values($skuArray);
        foreach ($products as $product) {
            $product->setPromoQty($promoSkuCounts[$product->getSku()]);
        }
        return $products;
    }

    /**
     * @param $value
     */
    function trimValue(&$value)
    {
        $value = trim($value);
    }

    /**
     * @return mixed
     */
    function getWidth()
    {
        return $this->_scopeConfig->getValue('ampromo/gift_images/gift_image_width');
    }

    /**
     * @return mixed
     */
    function getHeight()
    {
        return $this->_scopeConfig->getValue('ampromo/gift_images/gift_image_height');
    }
}

