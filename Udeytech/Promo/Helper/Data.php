<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Promo\Helper;

use Exception;
use Magento\Catalog\Model\Product\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Core\Controller\Front\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Udeytech\Promo\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const RULE_TYPE_ALL_SKU = 0;
    /**
     *
     */
    const RULE_TYPE_ONE_SKU = 1;
    /**
     * @var null
     */
    protected $_productsCache = null;
    /**
     * @var array
     */
    protected $_rules = array();
    /**
     * @var
     */
    protected $actionInRules;
    /**
     * @var StockStateInterface
     */
    protected $_stockStateInterface;
    /**
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var RuleRepositoryInterface
     */
    protected $_rule;
    /**
     * @var
     */
    protected $scopeConfig;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * Data constructor.
     * @param Context $context
     * @param StockStateInterface $stockStateInterface
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param CollectionFactory $productCollectionFactory
     * @param RuleRepositoryInterface $rule
     * @param Session $checkoutSession
     * @param Cart $cart
     * @param SessionManagerInterface $coreSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        StockStateInterface $stockStateInterface,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        Registry $registry,
        CollectionFactory $productCollectionFactory,
        RuleRepositoryInterface $rule,
        Session $checkoutSession,
        Cart $cart,
        SessionManagerInterface $coreSession,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_request = $request;
        $this->_registry = $registry;
        $this->_rule = $rule;
        $this->_cart = $cart;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_coreSession = $coreSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockStateInterface = $stockStateInterface;
        parent::__construct($context);
    }

    /**
     * @param $product
     * @param bool $super
     * @param bool $options
     * @param bool $bundleOptions
     * @param bool $ruleId
     * @param array $amgiftcardValues
     * @param int $qty
     * @param array $downloadableLinks
     * @param array $requestInfo
     */
    public function addProduct($product, $super = false, $options = false, $bundleOptions = false, $ruleId = false, $amgiftcardValues = array(), $qty = 1, $downloadableLinks = array(), $requestInfo = array())
    {
        $cart = $this->_getOrderCreateModel();
        $qty = $this->checkAvailableQty($product, $qty);
        if (($qty <= 0) && ($product->getStatus() == Status::STATUS_ENABLED)) {
            $this->showMessage(__("We apologize, but your free gift is not available at the moment", $product->getName()), false, true);
            return;
        }
        $requestInfo['qty'] = $qty;
        if (isset($requestInfo['options'])) {
            $requestInfo['options'] = array();
        }
        if ($super) {
            $requestInfo['super_attribute'] = $super;
        }
        if ($options) {
            $requestInfo['options'] = $options;
        } else {
            $requestInfo['ampromo_rule_id'] = $ruleId;
        }
        if ($bundleOptions) {
            $requestInfo['bundle_option'] = $bundleOptions;
        }
        if ($amgiftcardValues) {
            $requestInfo = array_merge($amgiftcardValues, $requestInfo);
        }
        if (count($downloadableLinks) > 0
            && $product->getTypeId() == 'downloadable'
        ) {
            $requestInfo['links'] = $downloadableLinks;
        }
        $requestInfo['options']['ampromo_rule_id'] = $ruleId;
        try {
            $cart->addProduct(+$product->getId(), $requestInfo);
            $cart->getQuote()->setTotalsCollectedFlag(false);
            $cart->getQuote()->getShippingAddress()->unsetData('cached_items_nonnominal');
            $cart->getQuote()->collectTotals();
            $cart->getQuote()->save();
            $this->_registry('ampromo/registry')->restore($product->getData('sku'));
            $this->showMessage(__("Free gift <b>%s</b> was added to your shopping cart", $product->getName()), false, true);
        } catch (Exception $e) {
            $this->showMessage($e->getMessage(), true, true);
        }
    }

    /**
     * @return bool
     */
    protected function _getOrderCreateModel()
    {
        $ret = false;
        if ($this->applyToAdminOrders()) {
            $ret = $this->_cart->get('adminhtml/sales_order_create');
        } else {
            $ret = $this->_cart->create();;
        }
        return $ret;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function applyToAdminOrders()
    {
        return $this->_storeManager->getStore()->isAdmin()
            && $this->scopeConfig->getValue('ampromo/general/apply_to_admin_orders');
    }

    /**
     * @param $product
     * @param $qtyRequested
     * @return mixed
     */
    public function checkAvailableQty($product, $qtyRequested)
    {
        $cart = $this->_getOrderCreateModel();
        $stockItem = $this->_stockStateInterface->get()->assignProduct($product);
        if (!$stockItem->getManageStock()) {
            return $qtyRequested;
        }
        $qtyAdded = 0;
        foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
            if ($item->getProductId() == $product->getId()) {
                $qtyAdded += $item->getQty();
            }
        }
        $qty = $stockItem->getStockQty() - $qtyAdded;
        return min($qty, $qtyRequested);
    }

    /**
     * @param $message
     * @param bool $isError
     * @param bool $showEachTime
     */
    public function showMessage($message, $isError = true, $showEachTime = false)
    {
        if (!$this->_scopeConfig->isSetFlag('ampromo/messages/errors') && $isError) {
            return;
        }
        if (!$this->_scopeConfig->isSetFlag('ampromo/messages/success') && !$isError) {
            return;
        }
        $all = $this->_checkoutSession->getMessages(false)->toString();
        if (false !== strpos($all, $message)) {
            return;
        }
        if ($isError && $this->_request->getRequest()->getParam('debug') !== null) {
            $this->_checkoutSession->addError($message);
        } else {
            $arr = $this->_checkoutSession->getAmpromoMessages();
            if (!is_array($arr)) {
                $arr = array();
            }
            if (!in_array($message, $arr) || $showEachTime) {
                if ($this->_request->getRequest()->isXmlHttpRequest()) {
                    if ($messageBlock = Mage::app()->getLayout()->getBlock('messages')) {
                        $messageBlock->addSuccess($message);
                    }
                } elseif ($this->_request->getRequest()->getModuleName() == 'amscheckoutfront') {
                    $this->_coreSession->addSuccess($message);
                } else {
                    $this->_checkoutSession->addSuccess($message);
                }
                $arr[] = $message;
                $this->_coreSession->setAmpromoMessages($arr);
            }
        }
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGiftcardAmounts($product)
    {
        $result = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $result[] = $this->_storeManager->getStore()->roundPrice($amount['website_value']);
        }
        sort($result);

        return $result;
    }

    /**
     * @param $pattern
     * @return string|string[]|null
     */
    public function processPattern($pattern)
    {
        $result = preg_replace_callback(
            '#{url\s+(?P<url>[\w/]+?)}#',
            array($this, 'replaceUrl'),
            $pattern
        );
        return $result;
    }

    /**
     * @param $matches
     * @return string
     */
    public function replaceUrl($matches)
    {
        return $this->_getUrl($matches['url']);
    }

    /**
     * @param null $value
     */
    public function updateNotificationCookie($value = null)
    {
        if ($value === null) {
            $newItems = $this->getNewItems();
            if (!is_array($newItems)) {
                $newItems = $newItems->getData();
            }
            $value = empty($newItems) ? 0 : 1;
        }
        Mage::getModel('core/cookie')->set('am_promo_notification', $value, null, null, null, null, false);
    }

    /**
     * @param bool $flushCache
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Framework\Data\Collection\AbstractDb|null
     */
    public function getNewItems($flushCache = false)
    {
        if ($this->_productsCache === null || $flushCache) {
            $items = $this->_registry->registry('ampromo/registry')->getLimits();
            $groups = $items['_groups'];
            unset($items['_groups']);
            if (!$items && !$groups) {
                return array();
            }
            $allowedSku = array_keys($items);
            $sku2rules = array();

            foreach ($items as $sku => $item) {
                $sku2rules[$item['sku']] = $item['rule_id'];
            }
            foreach ($groups as $ruleId => $rule) {
                $allowedSku = array_merge($allowedSku, $rule['sku']);
                foreach ($allowedSku as &$sku)
                    $sku = (string)$sku;
                if (is_array($rule['sku'])) {
                    foreach ($rule['sku'] as $sku) {
                        $sku2rules[$sku] = $rule['rule_id'];
                    }
                }
            }
            $addAttributes = array();
//            if ($this->isModuleEnabled('Amasty_GiftCard')) {
//                $addAttributes = $this->helper('amgiftcard')->getAmGiftCardOptionsInCart();
//            }
            $allowedSku = array_map('strval', $allowedSku);
            $products = $this->_productCollectionFactory->create()->addFieldToFilter('sku', array('in' => $allowedSku))
                ->addAttributeToSelect(array_merge(array('name', 'small_image', 'status', 'visibility', 'price',
                    'links_purchased_separately', 'links_exist'), $addAttributes));
            foreach ($products as $key => $product) {
                $productSku = $product->getSku();
                $ruleId = isset($sku2rules[$productSku]) ? $sku2rules[$productSku] : null;
                $rule = $this->getRule($ruleId);
                if (!in_array($product->getTypeId(), array('simple', 'configurable', 'virtual', 'bundle', 'amgiftcard', 'downloadable', 'giftcard', 'amstcred'))) {
                    $this->showMessage($this->__("We apologize, but products of type <b>%s</b> are not supported", $product->getTypeId()));
                    $products->removeItemByKey($key);
                }
                if (($product->getStatus() == Status::STATUS_ENABLED) && (!$product->isSalable()
                        || !$this->checkAvailableQty($product, 1))
                ) {
                    $this->showMessage(__("We apologize, but your free gift is not available at the moment"));
                    $products->removeItemByKey($key);
                } else if ($product->getStatus() != Status::STATUS_ENABLED) {
                    $products->removeItemByKey($key);
                }
                foreach ($product->getProductOptionsCollection() as $option) {
                    $option->setProduct($product);
                    $product->addOption($option);
                }
                if ($rule && $rule->getAmpromoShowOrigPrice()) {
                    $product->setAmpromoShowOrigPrice($rule->getAmpromoShowOrigPrice());
                    $price = $product->getPrice();
//                    if ($product->getTypeId() == 'giftcard') {
//                        $_amount = Mage::helper("ampromo")->getGiftcardAmounts($product);
//                        $price = array_shift($_amount);
//                    }
                    $product->setSpecialPrice($this->getDiscountPrice($rule, $price, $product));
                    $product->setFinalPrice($product->getSpecialPrice());
                }
                $product->setData('ampromo_rule', $rule);
            }
            $this->_productsCache = $products;
        }
        return $this->_productsCache;
    }

    /**
     * @param $ruleId
     * @return mixed
     */
    public function getRule($ruleId)
    {
        if (!isset($this->_rules[$ruleId])) {
            $this->_rules[$ruleId] = $this->_rule->create();
            $this->_rules[$ruleId]->load($ruleId);
        }

        return $this->_rules[$ruleId];
    }

    /**
     * @param $rule
     * @param $price
     * @param null $product
     * @return float|int|mixed
     */
    function getDiscountPrice($rule, $price, $product = null)
    {
        $ampromoDiscountValue = $rule->getAmpromoDiscountValue();
        $discountValue = trim($ampromoDiscountValue);
        $minPrice = $rule->getAmpromoMinPrice();

        if (!empty($discountValue)) {
            if (!$product->getIsProceedDiscount()) {
                $delta = 0;
                preg_match('/[0-9]+(\.[0-9][0-9]?)?/', $discountValue, $matches);
                $operator = $discountValue[0];

                if ('%' == $discountValue[strlen($discountValue) - 1] && $matches[0]) {
                    $delta = $price * $matches[0] / 100;
                    $operator = "-";
                } else {
                    $delta = $matches[0];
                }

                switch ($operator) {
                    case '+':
                        $price = $price + $delta;
                        break;
                    case '-':
                        $price = $price - $delta;
                        break;
                    case '*':
                        $price = $price * $delta;
                        break;
                    case '/':
                        $price = $price / $delta;
                        break;
                    default:
                        $price = $delta;
                        break;
                }

                if ($price < 0) {
                    $price = 0;
                }
                if ($product->getProductType() == 'bundle') {
                    $product->setIsProceedDiscount(true);
                }
            }
        } else {
            $price = 0;
        }

        if (!empty($minPrice) && $price < $minPrice) {
            $price = $minPrice;
        }

        return $price;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlParams()
    {
        if ($this->_request->getRequest()->isXmlHttpRequest()) {
            $returnUrl = $this->getRequest()->getServer('HTTP_REFERER');
        } else {
            $returnUrl = $this->getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        }
        $params = array(Action::PARAM_NAME_URL_ENCODED => $this->_helper->urlEncode($returnUrl));
        $params['_secure'] = $this->_storeManager->getStore()->isCurrentlySecure();
        return $params;
    }
}

