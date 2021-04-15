<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

class Observer implements ObserverInterface {
    /**
     *
     */
    const EQUALS = 0;
    /**
     *
     */
    const CONTAINS = 1;
    /**
     * @var
     */
    protected $registry;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollection;
    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cartFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var array
     */
    protected $_isHandled = array();
    /**
     * @var array
     */
    protected $_toAdd = array();
    /**
     * @var array
     */
    protected $_itemsWithDiscount = array();
    /**
     * @var
     */
    protected $_calcHelper;
    /**
     * @var array
     */
    protected $_rules = array();
    /**
     * @var bool
     */
    protected $_onCollectTotalAfterBusy = false;
    /**
     * @var array
     */
    protected $_bundleProductsInCart = array();
    /**
     * @var bool
     */
    protected $_selfExecuted = false;
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var array
     */
    protected $_arrayWithSimpleAction = array(
        'buy_x_get_y_percent',
        'buy_x_get_y_fixdisc',
        'buy_x_get_y_fixed',
        'buy_x_get_n_percent',
        'buy_x_get_n_fixdisc',
        'buy_x_get_n_fixed',
        'setof_percent',
        'setof_fixed',
        'ampromo_items',
        'ampromo_cart',
        'ampromo_spent',
        'ampromo_product'
    );
    /**
     * @var array
     */
    protected $_arrayWithProductSet = array('setof_percent', 'setof_fixed');
    /**
     * Observer constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ){
        $this->_registry = $registry;
        $this->_cartFactory = $cartFactory;
        $this->_auth = $auth;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollection = $productCollection;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        // TODO: Implement execute() method.
    }

    /**
     * @param $item
     * @return mixed
     */
    protected function _getParentItem($item)
    {
        if ($item->getParentItemId()
            && $item->getParentItem()->getProductType() == 'bundle'
            && !in_array($item->getParentItem()->getSku(), $this->_bundleProductsInCart)
        ){
            $item = $item->getParentItem();
            $this->_bundleProductsInCart[] = $item->getSku();
        }
        return $item;
    }
    /**
     * @param $observer
     * @return $this|bool
     */
    public function handleValidation($observer){
        $rule = $observer->getEvent()->getRule();
        $address = $observer->getEvent()->getAddress();
        if ($rule->getSimpleAction() == 'ampromo_product') {
            try {
                $eventItem = $observer->getEvent()->getItem();
                $item = $this->_getParentItem($eventItem);

                if ($item->getIsPromo() || $this->_skip($item, $address)) {
                    return false;
                }

                $discountStep = max(1, $rule->getDiscountStep());
                $maxDiscountQty = 100000;
                if ($rule->getDiscountQty()) {
                    $maxDiscountQty = intVal(max(1, $rule->getDiscountQty()));
                }

                $discountAmount = max(1, $rule->getDiscountAmount());
                $qty = min(floor($item->getQty() / $discountStep) * $discountAmount, $maxDiscountQty);

                if ($item->getParentItemId()) {
                    return false;
                }

                if ($qty < 1) {
                    return false;
                }

                Mage::getSingleton('ampromo/registry')->addPromoItem(
                    $item->getSku(),
                    $qty,
                    $rule
                );

            } catch (\Exception $e) {
                $hlp = $this->helper('Udeytech\Promo\Helper\Data');
                $hlp->showMessage(__(
                    'We apologize, but there is an error while adding free items to the cart: %s',
                    $e->getMessage()
                ));
                return false;
            }
        }
        if(!in_array($rule->getSimpleAction(), array('ampromo_items','ampromo_cart','ampromo_spent',))
        ){
            return $this;
        }
        if (isset($this->_isHandled[$rule->getId()])) {
            return $this;
        }
        $this->_isHandled[$rule->getId()] = true;
        $promoSku = $rule->getPromoSku();
        if (!$promoSku) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        $qty = $this->_getFreeItemsQty($rule, $quote, $address);
        if (!$qty) {
            return $this;
        }

        if ($rule->getAmpromoType() == \Udeytech\Promo\Helper\Data::RULE_TYPE_ONE_SKU) {
            $this->_registry->registry('ampromo/registry')->addPromoItem(
                array_map('trim', preg_split('/\s*,\s*/', $promoSku, -1, PREG_SPLIT_NO_EMPTY)),
                $qty,$rule);
        } else {
            $promoSku = explode(',', $promoSku);
            foreach ($promoSku as $sku) {
                $sku = trim($sku);
                // $sku = strtolower($sku);
                if (!$sku) {
                    continue;
                }

                Mage::getSingleton('ampromo/registry')->addPromoItem($sku, $qty, $rule);
            }
        }

        return $this;
    }
    /**
     * @param $item
     * @param $address
     * @return bool
     */
    protected function _skip($item, $address)
    {
        $skipSpecialPrice = Mage::getStoreConfig('ampromo/limitations/skip_special_price');
        $skipChildSpecialPrice = Mage::getStoreConfig('ampromo/limitations/skip_special_price_configurable');

        if (!$skipSpecialPrice && !$skipChildSpecialPrice) {
            return false;
        }
        if ($skipSpecialPrice
            && ($item->getIsAmpromoGift()
                || ($item->getProduct()->getSpecialPrice()
                    && $item->getProduct()->getPrice() != $item->getProduct()->getSpecialPrice()))
        ) {
            return true;
        }

        if ($item->getProductType() == 'bundle') {
            return false;
        }

        $this->collectItemsWithDiscount($item, $address);

        if ($skipChildSpecialPrice && $item->getProductType() == "configurable") {
            foreach ($item->getChildren() as $child) {
                if (in_array($child->getProductId(), $this->_itemsWithDiscount)) {
                    return true;
                }
            }
        }

        if (!in_array($item->getProductId(), $this->_itemsWithDiscount)) {
            return false;
        }

        return true;
    }
    /**
     * @param $item
     * @param $address
     * @return array|bool|null
     */
    protected function collectItemsWithDiscount($item, $address)
    {
        if ($this->_itemsWithDiscount === null || count($this->_itemsWithDiscount) == 0) {
            $this->_itemsWithDiscount = $productIds = array();
            foreach ($this->_getAllItems($address) as $addressItem) {
                $productIds[] = $addressItem->getProductId();
            }
            if (!$productIds) {
                return false;
            }
            $customerGroupId    = $item->getProduct()->getCustomerGroupId();
            $storeId            = $item->getProduct()->getStoreId();
            $websiteId          = $this->_storeManager->load($storeId)->getWebsiteId();
            $productsCollection = $this->_productCollection->getCollection()
                ->addPriceData($customerGroupId, $websiteId)
                ->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('price', array('gt' => new Zend_Db_Expr('final_price')));
            foreach ($productsCollection as $product) {
                $this->_itemsWithDiscount[] = $product->getId();
            }
        }
        return $this->_itemsWithDiscount;
    }
    /**
     * @param $address
     * @return mixed
     */
    protected function _getAllItems($address)
    {
        $items = $address->getAllNonNominalItems();
        if (!$items) {
            $items = $address->getAllVisibleItems();
        }
        if (!$items) {
            $cart = $this->_cartFactory->create();
            $items = $cart->getItems();
        }
        return $items;
    }
    /**
     * @param $observer
     */
    public function onCollectTotalsBefore($observer)
    {
        $this->_registry->registry('ampromo/registry')->reset();
    }
    /**
     * @param $observer
     * @return bool
     */
    public function onAddressCollectTotalsAfter($observer)
    {
        if ($this->_selfExecuted) {
            return true;
        }
        $quote = $observer->getQuoteAddress()->getQuote();
        $items = $quote->getAllItems();
        $realCount = 0;
        foreach ($items as $item) {
            if ($item->getIsPromo()) {
                $item->isDeleted(false);
                $this->resetWeee($item);
            } else {
                $realCount++;
            }
        }
        if ($realCount == 0) {
            $this->_selfExecuted = true;
            foreach ($items as $item) {
                $itemId = $item->getItemId();
                $quote->removeItem($itemId)->save();
            }
        }
        if ($this->_scopeConfig->isSetFlag('ampromo/general/auto_add')) {
            $toAdd = $this->_registry->registry('ampromo/registry')->getPromoItems();
            if (is_array($toAdd)) {
                unset($toAdd['_groups']);
                foreach ($items as $item) {
                    $sku = $item->getProduct()->getData('sku');
                    if (!isset($toAdd[$sku])) {
                        continue;
                    }
                    //$qtyIncreased = isset($toAdd[$sku]['qtyIncreased']) ? $toAdd[$sku]['qtyIncreased'] : false;
                    /* weak code - for avoid issue with added to cart automatically */
                    $qtyIncreased = true;
                    if ($item->getIsPromo()) {
                        if (!$qtyIncreased) {
                            unset($toAdd[$sku]); // to allow to decrease qty
                        } else {
                            $toAdd[$sku]['qty'] -= $item->getQty();
                        }
                    }
                }
                $deleted = array();
                if ($observer->getQuoteAddress()->getAddressType() === 'shipping') {
                    $rulesIds = explode(',', $quote->getAppliedRuleIds());
                    $deleted = $this->_registry->registry('ampromo/registry')->getDeletedItems($rulesIds);
                }
                $this->_toAdd = array();
                foreach ($toAdd as $sku => $item) {
                    if ($item['qty'] > 0 && $item['auto_add'] && !isset($deleted[$sku])) {
                        $product = $this->_productCollection->create()->loadByAttribute('sku', $sku);
                        if (isset($this->_toAdd[$product->getId()])) {
                            $this->_toAdd[$product->getId()]['qty'] += $item['qty'];
                        } else {
                            $this->_toAdd[$product->getId()] = array(
                                'product' => $product,
                                'qty' => $item['qty']
                            );
                        }
                    }
                }
            }
        }
    }
    /**
     * @param $item
     */
    public function resetWeee(&$item)
    {
        $this->helper('Udeytech\Promo\Helper\Weee')->setApplied($item, array());

        $item->setBaseWeeeTaxDisposition(0);
        $item->setWeeeTaxDisposition(0);

        $item->setBaseWeeeTaxRowDisposition(0);
        $item->setWeeeTaxRowDisposition(0);

        $item->setBaseWeeeTaxAppliedAmount(0);
        $item->setBaseWeeeTaxAppliedRowAmount(0);

        $item->setWeeeTaxAppliedAmount(0);
        $item->setWeeeTaxAppliedRowAmount(0);
    }
    /**
     * @param $observer
     */
    public function onQuoteRemoveItem($observer)
    {
        $id = (int)Mage::app()->getRequest()->getParam('id');
        $item = $observer->getEvent()->getQuoteItem();
        $registry = $this->_registry->registry('ampromo/registry');
        if (($item->getId() == $id) && $item->getIsPromo() && !$item->getParentId()) {
            $sku = $item->getProduct()->getData('sku');
            $registry->deleteProduct($sku);
        } else {
            $rulesIds = explode(',', $item->getQuote()->getAppliedRuleIds());
            $registry->checkDeletedItems($rulesIds);
        }
    }
    /**
     * @param $observer
     * @return $this
     */
    public function decrementUsageAfterPlace($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return $this;
        }
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        if ($ruleIds && Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->_setItemPrefix($order->getAllItems());
        }
        $ruleCustomer = null;
        $customerId = $order->getCustomerId();
        if (($order->getDiscountAmount() == 0) && (count($ruleIds) >= 1)) {
            foreach ($ruleIds as $ruleId) {
                if (!$ruleId) {
                    continue;
                }
                $rule = Mage::getModel('salesrule/rule');
                $rule->load($ruleId);
                if ($rule->getId()) {
                    $rule->setTimesUsed($rule->getTimesUsed() + 1);
                    $rule->save();

                    if ($customerId) {
                        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                        $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

                        if ($ruleCustomer->getId()) {
                            $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + 1);
                        } else {
                            $ruleCustomer
                                ->setCustomerId($customerId)
                                ->setRuleId($ruleId)
                                ->setTimesUsed(1);
                        }
                        $ruleCustomer->save();
                    }
                }
            }
            $coupon = Mage::getModel('salesrule/coupon');
            /** @var Mage_SalesRule_Model_Coupon */
            $coupon->load($order->getCouponCode(), 'code');
            if ($coupon->getId()) {
                $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
                $coupon->save();
                if ($customerId) {
                    $couponUsage = Mage::getResourceModel('salesrule/coupon_usage');
                    $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
                }
            }
        }
    }
    /**
     * @return mixed
     */
    protected function _getCalcHelper()
    {
        if (!$this->_calcHelper) {
            $this->_calcHelper = $this->helper("Udeytech/Promo\Helper\Celc");
        }

        return $this->_calcHelper;
    }
    /**
     * @param $rule
     * @param $quote
     * @param $address
     * @return float|int|mixed
     */
    protected function _getFreeItemsQty($rule, $quote, $address)
    {
        $amount = max(1, $rule->getDiscountAmount());
        $qty = 0;
        $skipSpecialPrice = $this->_scopeConfig->getValue('ampromo/limitations/skip_special_price');
        if ('ampromo_cart' == $rule->getSimpleAction()) {
            if (!$skipSpecialPrice) {
                $qty = $amount;
            } else {
                $allVisibleItems = $quote->getAllVisibleItems();
                foreach ($allVisibleItems as $item) {
                    if (!$this->_skip($item, $address)) {
                        $qty = $amount;
                    }
                }
            }
        } else if ('ampromo_spent' == $rule->getSimpleAction()) {
            $step = $rule->getDiscountStep();
            if (!$step) {
                return 0;
            }
            $qty = floor($this->_getCalcHelper()->getQuoteSubtotal($quote, $rule) / $step) * $amount;
            $max = $rule->getDiscountQty();
            if ($max) {
                $qty = min($max, $qty);
            }
            return $qty;
        } else {
            $step = max(1, $rule->getDiscountStep());
            $allVisibleItems = $quote->getAllVisibleItems();
            foreach ($allVisibleItems as $item) {
                if (!$item)
                    continue;
                if ($item->getIsPromo())
                    continue;
                if ($this->_skip($item, $address)) {
                    continue;
                }
                if (!$rule->getActions()->validate($item)) {
                    continue;
                }
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getProduct()->getParentProductId()) {
                    continue;
                }
                $qty = $qty + $item->getQty();
            }
            $qty = floor($qty / $step) * $amount;
            $max = $rule->getDiscountQty();
            if ($max) {
                $qty = min($max, $qty);
            }
        }
        return $qty;
    }
    /**
     * @param $observer
     */
    public function onProductAddAfter($observer)
    {
        $items = $observer->getItems();

        $this->_setItemPrefix($items);

        foreach ($items as $item) {
            if ($item->getIsPromo())
                $item->setNoDiscount(true);
        }
    }
    /**
     * @param $observer
     */
    public function onAdminhtmlSalesOrderCreateProcessDataBefore($observer)
    {
        $this->_registry->registry('ampromo/registry')->backup();
    }
    /**
     * @param $observer
     */
    public function onCheckoutCartUpdateItemsBefore($observer)
    {
        if (is_array($observer->getInfo())) {

            $items = $this->_getSession()->getQuote()->getAllVisibleItems();

            foreach ($observer->getInfo() as $itemId => $info) {
                if ($info['qty'] == 0) {
                    foreach ($items as $item) {
                        if ($item->getId() == $itemId) {
                            Mage::getSingleton('ampromo/registry')->deleteProduct($item->getSku());
                            break;
                        }
                    }
                }
            }
        }
        $this->_registry->registry('ampromo/registry')->backup();
    }
    /**
     * @return |null
     */
    protected function _getSession()
    {
        $session = null;
        if (Mage::helper('ampromo')->applyToAdminOrders()) {
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            $session = Mage::getSingleton('checkout/session');
        }

        return $session;
    }
    /**
     * @param $observer
     */
    public function onCollectTotalsAfter($observer)
    {
        if (!$this->_onCollectTotalAfterBusy) {
            $this->_onCollectTotalAfterBusy = true;
            if (!$this->_getSession()->getQuote() || !$this->_getSession()->getQuote()->getId())
                return;

            $quote = $observer->getQuote();
            if ($quote->getIsFake()) {
                return;
            }

            Mage::helper('ampromo')->updateNotificationCookie();

            $allowedItems = Mage::getSingleton('ampromo/registry')->getPromoItems();
            $cart = Mage::getSingleton('checkout/cart');


            $customMessage = Mage::getStoreConfig('ampromo/general/message');

            foreach ($this->_toAdd as $item) {
                $product = $item['product'];
                //$productSku = strtolower($product->getSku());
                $productSku = $product->getSku();

                $ruleId = $allowedItems[$productSku] ? $allowedItems[$productSku]['rule_id'] : null;

                Mage::helper('ampromo')->addProduct(
                    $product,
                    false, false, false, $ruleId, array(),
                    $item['qty']
                );
            }

            $this->_toAdd = array();

            foreach ($observer->getQuote()->getAllItems() as $item) {
                if ($item->getIsPromo()) {
                    $ruleLabel = $item->getRule()->getStoreLabel();
                    $ruleMessage = !empty($ruleLabel) ? $ruleLabel : $customMessage;

                    if ($item->getParentItemId())
                        continue;

                    $sku = $item->getProduct()->getData('sku');
                    //$sku = strtolower($sku);

                    if (isset($allowedItems['_groups'][$item->getRuleId()])) // Add one of
                    {
                        if ($allowedItems['_groups'][$item->getRuleId()]['qty'] <= 0) {
                            $cart->removeItem($item->getId());
                        } else if ($item->getQty() > $allowedItems['_groups'][$item->getRuleId()]['qty']) {
                            $item->setQty($allowedItems['_groups'][$item->getRuleId()]['qty']);
                        }
                        if ($ruleMessage)
                            $item->setMessage($ruleMessage);

                        $allowedItems['_groups'][$item->getRuleId()]['qty'] -= $item->getQty();
                    } else if (isset($allowedItems[$sku]) &&
                        $allowedItems[$sku]['rule_id'] == $item->getRuleId()
                    ) // Add all of
                    {
                        if ($allowedItems[$sku]['qty'] <= 0) {
                            $cart->removeItem($item->getId());
                        } else if ($item->getQty() > $allowedItems[$sku]['qty']) {
                            $item->setQty($allowedItems[$sku]['qty']);
                        }
                        if ($ruleMessage)
                            $item->setMessage($ruleMessage);

                        $allowedItems[$sku]['qty'] -= $item->getQty();
                    } else
                        $cart->removeItem($item->getId());
                }
            }

            $this->updateQuoteTotalQty($observer->getQuote());
            $this->_onCollectTotalAfterBusy = false;
        }
    }
    /**
     * @param Mage_Sales_Model_Quote $quote
     */
    public function updateQuoteTotalQty(Mage_Sales_Model_Quote $quote)
    {
        $quote->setItemsCount(0);
        $quote->setItemsQty(0);
        $quote->setVirtualItemsQty(0);

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $children = $item->getChildren();
            if ($children && $item->isShipSeparately()) {
                foreach ($children as $child) {
                    if ($child->getProduct()->getIsVirtual()) {
                        $quote->setVirtualItemsQty($quote->getVirtualItemsQty() + $child->getQty() * $item->getQty());
                    }
                }
            }

            if ($item->getProduct()->getIsVirtual()) {
                $quote->setVirtualItemsQty($quote->getVirtualItemsQty() + $item->getQty());
            }
            $quote->setItemsCount($quote->getItemsCount() + 1);
            $quote->setItemsQty((float)$quote->getItemsQty() + $item->getQty());
        }
    }
    /**
     * @param $observer
     */
    public function onOrderPlaceBefore($observer)
    {
        $order = $observer->getOrder();

        $this->_setItemPrefix($order->getAllItems());
    }
    /**
     * @param $id
     * @return mixed
     */
    protected function _loadRule($id)
    {
        if (!isset($this->_rules[$id])) {
            $this->_rules[$id] = Mage::getModel('salesrule/rule')->load($id);
        }
        return $this->_rules[$id];
    }
    /**
     * @param $items
     */
    protected function _setItemPrefix($items)
    {
        $prefix = Mage::getStoreConfig('ampromo/general/prefix');
        foreach ($items as $item) {
            $buyRequest = $item->getBuyRequest();
            $labelName  = $item->getLabelName();
            if (isset($buyRequest['options']['ampromo_rule_id'])) {
                $rule = $this->_loadRule($buyRequest['options']['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
            } elseif (isset($buyRequest['ampromo_rule_id'])
                && !isset($labelName)
            ) {
                $rule = $this->_loadRule($buyRequest['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
                $item->setLabelName(1);
            }
        }
    }
    /**
     * @param $rule
     * @param $item
     * @param $prefix
     */
    protected function _generateNameWithLabel($rule, $item, $prefix)
    {
        $ruleLabel = $rule->getAmpromoPrefix();
        $rulePrefix = !empty($ruleLabel) ? $ruleLabel : $prefix;
        $item->setName($rulePrefix . ' ' . $item->getName());
    }
    /**
     * @param $observer
     */
    public function onCartItemUpdateBefore($observer)
    {
        $request = Mage::app()->getRequest();

        $id = (int)$request->getParam('id');
        $item = Mage::getSingleton('checkout/cart')->getQuote()->getItemById($id);

        if ($item->getId() && $item->getIsPromo()) {
            $options = $request->getParam('options');
            $options['ampromo_rule_id'] = $item->getRuleId();
            $request->setParam('options', $options);
        }
    }
    /**
     * @param $observer
     */
    public function onCheckoutSubmitAllAfter($observer)
    {
        Mage::getSingleton('ampromo/registry')->reset();
        Mage::helper('ampromo')->updateNotificationCookie(0);
    }
    /**
     * @param $observer
     */
    public function salesRulePrepareSave($observer)
    {
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_top_banner_img');
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_after_name_banner_img');
        $this->_savePromoRuleImage($observer->getRequest(), 'ampromo_label_img');
    }
    /**
     * @param $observer
     */
    public function saveBefore($observer)
    {
        $controllerAction = $observer->getRule()->getData();
        if ($controllerAction['simple_action'] == 'ampromo_cart') {
            $data = $observer->getRule()->getData();
            $r = array(
                'type' => 'salesrule/rule_condition_product_combine',
                'attribute' => null,
                'operator' => null,
                'value' => '1',
                'is_value_processed' => null,
                'aggregator' => 'any',
                'conditions' =>
                    array(),
            );
            $data['actions_serialized'] = serialize($r);
            $observer->getRule()->setData($data);
        }
    }
    /**
     * @param $observer
     */
    public function saveAfter($observer)
    {
        $rule = $observer->getRule();
        $conditions = $rule->getConditions()->asArray();
        if (!Mage::helper('core')->isModuleEnabled('Amasty_Rules')) {

            $unsafeIs = 0;
            if (isset($conditions['conditions'])) {
                $unsafeIs = $this->checkForIs($conditions['conditions']);
            }

            $actions = $rule->getActions()->asArray();
            if (isset($actions['conditions']) && !$unsafeIs) {
                $unsafeIs = $this->checkForIs($actions['conditions']);
            }

            if ($unsafeIs) {
                Mage::getSingleton('adminhtml/session')->addNotice('It is more safe to use `is one of` operator and not `is` for comparison.  Please correct if the rule does not work as expected.');
            }
        }

        if (Mage::getStoreConfig('ampromo/messages/show_stock_warning')) {
            $skuArray          = array();
            $checkActionForSku = array(array(), array());
            $promoSku          = $rule->getPromoSku();
            $trimPromoSku        = trim($promoSku);
            if (!empty($trimPromoSku)
                && in_array($rule->getSimpleAction(), $this->_arrayWithSimpleAction)
            ) {
                $skuArray = $this->_checkActionForPromoSku($rule);
            }

            $actions[] = $conditions;
            $checkActionForSku = $this->_checkActionForSku($actions);

            if ($skuArray) {
                $checkActionForSku[self::EQUALS] = array_merge($checkActionForSku[self::EQUALS], $skuArray);
            }
            if (!empty($checkActionForSku[self::EQUALS])
                || !empty($checkActionForSku[self::CONTAINS])
            ) {
                $skuArray = $this->_checkForSku($checkActionForSku);
            }
            $getParamBack = Mage::app()->getRequest()->getParam('back');

            if ($skuArray && $rule->getIsActive() && $getParamBack) {
                $this->_generateMessage($skuArray);
            }
        }
    }
    /**
     * @param $request
     * @param $file
     */
    protected function _savePromoRuleImage($request, $file)
    {
        if ($data = $request->getPost()) {

            if (isset($data[$file]) && isset($data[$file]['delete'])) {
                $data[$file] = null;
            } else {

                if (isset($_FILES[$file]['name']) && $_FILES[$file]['name'] != '') {

                    $fileName = Mage::helper("ampromo/image")->upload($file);

                    $data[$file] = $fileName;
                } else {
                    if (isset($data[$file]['value']))
                        $data[$file] = basename($data[$file]['value']);
                }
            }

            $request->setPost($data);
        }
    }
    /**
     * @param $array
     * @return bool
     */
    protected function checkForIs($array)
    {
        foreach ($array as $element) {
            if ($element['operator'] == '==' && strpos($element['value'], ',') !== FALSE) {
                return true;
            }
            if (isset($element['conditions'])) {
                $this->checkForIs($element['conditions']);
            }
        }
        return false;
    }
    /**
     * @param $rule
     * @return array
     */
    protected function _checkActionForPromoSku($rule){
        $strWithSku    = $rule->getPromoSku();
        $actions       = $rule->getActions()->getData('actions');
        $outOfStockSku = array();
        foreach ($actions as $action) {
            if ($action['attribute'] == "quote_item_sku") {
                $strWithItemsSku = $action['value'];
                $outOfStockSku   = $this->_convertAndFormat($strWithItemsSku);
            }
        }
        if ($strWithSku != "") {
            $arrayWithSku  = $this->_convertAndFormat($strWithSku);
            $outOfStockSku = array_merge($outOfStockSku, $arrayWithSku);
        }
        return array_unique($outOfStockSku);
    }
    /**
     * @param $skusFromRules
     * @return mixed
     */
    protected function _checkForSku($skusFromRules)
    {
        $strWithLikeSkus = "";
        $strWithEqSkus   = "";
        $arrayWithEqSkus = array();

        $collectionWithProducts =
            Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->joinField(
                    'qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left')
                ->joinField(
                    'use_config_manage_stock',
                    'cataloginventory/stock_item',
                    'use_config_manage_stock',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left')
                ->joinField(
                    'stock_status',
                    'cataloginventory/stock_status',
                    'stock_status',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left')
                ->addFieldToFilter('use_config_manage_stock', 1)
                ->addFieldToFilter('type_id', array('nin' => array('bundle', 'configurable', 'grouped')));
        /*
         * get arrays with sku
         * like not working with arrays
         * generate string with all likes
         * */
        if (!empty($skusFromRules[self::EQUALS])) {
            $strWithLikeSkus = "e.sku IN ('" . implode("', '", $skusFromRules[self::EQUALS]) . "')";
            if (!empty($skusFromRules[self::CONTAINS])) {
                $strWithLikeSkus .= " OR ";
            }
        }

        if (!empty($skusFromRules[self::CONTAINS])) {
            foreach ($skusFromRules[self::CONTAINS] as $skuFromRules) {
                $arrayWithEqSkus[] = "e.sku LIKE '%" . $skuFromRules . "%'";
            }
            $strWithEqSkus = implode($arrayWithEqSkus, " OR ");
        }

        $strWithResultQuery = $strWithLikeSkus . $strWithEqSkus;
        $collectionWithProducts->getSelect()
            ->where($strWithResultQuery)
            ->where("at_qty.qty <= 0 OR at_stock_status.stock_status <> 1")
            ->order('sku', 'ASC');

        $resultArray = $collectionWithProducts->getData();

        return $resultArray;
    }
    /**
     * @param $observer
     */
    public function prePromoQuoteIndex($observer){
        if (Mage::getStoreConfig('ampromo/messages/show_stock_warning')) {
            $resultArray          = array();
            $actionsAndConditions = array();
            $rulesCollection      = Mage::getModel('salesrule/rule')
                ->getCollection()
                ->addFieldToSelect('actions_serialized')
                ->addFieldToSelect('conditions_serialized')
                ->addFieldToSelect('promo_sku')
                ->addFieldToSelect('simple_action')
                ->addFieldtoFilter('is_active', 1);

            $rulesData    = $rulesCollection->getData();
            $arrayWithSku = array();
            foreach ($rulesData as $rule) {
                if (isset($rule['promo_sku'])
                    && in_array($rule['simple_action'], $this->_arrayWithSimpleAction)
                ) {
                    $arrayWithSku = array_merge($arrayWithSku, $this->_convertAndFormat($rule['promo_sku']));
                }
                if (!in_array($rule['simple_action'], $this->_arrayWithProductSet)) {
                    $actionsAndConditions[] = unserialize($rule['actions_serialized']);
                    $actionsAndConditions[] = unserialize($rule['conditions_serialized']);
                }
            }
            $skuArray = $this->_checkActionForSku($actionsAndConditions, $arrayWithSku);
            if (!empty($skuArray[self::EQUALS])
                || !empty($skuArray[self::CONTAINS])
            ) {
                $resultArray = $this->_checkForSku($skuArray);
            }

            if ($resultArray) {
                $this->_generateMessage($resultArray);
            }
        }
    }
    /**
     * @param $actionsAndConditions
     * @param null $promoSkus
     * @return array
     */
    protected function _checkActionForSku($actionsAndConditions, $promoSkus = null){
        $skus = $this->_recGetArrayWithSkus($actionsAndConditions, 'value');
        $arrayWithOutOfStock = array(array(), array());
        if (!empty($skus)) {
            $count = count($skus);
            for ($i = 0; $i < $count; $i ++) {
                $skus[$i] = array_unique($skus[$i]);
                foreach ($skus[$i] as $sku) {
                    if (!is_array($sku)) {
                        $arrayWithOutOfStock[$i] = array_merge(
                            $arrayWithOutOfStock[$i],
                            $this->_convertAndFormat($sku)
                        );
                    }
                }
            }
        }
        if ($promoSkus) {
            $arrayWithOutOfStock[self::EQUALS] = array_merge($arrayWithOutOfStock[self::EQUALS], $promoSkus);
        }
        return $arrayWithOutOfStock;
    }
    /**
     * @param $sku
     * @return array
     */
    protected function _convertAndFormat($sku)
    {
        $skusFromRules = explode(',', $sku);
        $skusFromRules = array_map('trim', $skusFromRules);

        return $skusFromRules;
    }
    /**
     * @param $outOfStockSkus
     */
    protected function _generateMessage($outOfStockSkus)
    {
        $arrayWithLinks = array();
        if ($outOfStockSkus) {
            foreach ($outOfStockSkus as $outOfStockSku) {
                $url = Mage::helper('adminhtml')->getUrl('/catalog_product/edit',
                    array('id' => $outOfStockSku['entity_id']));
                $arrayWithLinks[] = '<a href="' . $url . '"target="_blank">' . $outOfStockSku['sku'] . '</a>';
            }
            $strWithLinks = implode($arrayWithLinks, ', ');
            if ($strWithLinks != "") {
                $message = Mage::helper('ampromo')->__(
                    "Please notice, the %s have stock quantity <= 0 or are \"Out of stock\". That may interfere in proper rule execution.",
                    $strWithLinks);
                Mage::getSingleton('adminhtml/session')->addWarning($message);
            }
        }
    }
    /**
     * @param $conditions
     * @param $searchFor
     * @return array|null
     */
    protected function _recGetArrayWithSkus($conditions, $searchFor)
    {
        static $arrayWithSku = array(array(), array());
        static $arrayWithEqualSkus = array();
        foreach ($conditions as $key => $condition) {
            if ($key == $searchFor
                && is_string($condition)
                && $condition != ""
            ) {
                if ($conditions['attribute'] == 'sku'
                    || $conditions['attribute'] == 'quote_item_sku'
                ) {
                    if ($conditions['operator'] == "{}"
                        || $conditions['operator'] == "!{}"
                    ) {
                        $arrayWithEqualSkus[] = $condition;
                    } else {
                        $arrayWithSku[self::EQUALS][] = $condition;
                    }
                }
            }
            if (is_array($conditions[$key])) {
                $this->_recGetArrayWithSkus($condition, $searchFor);
            }
        }
        if (!empty($arrayWithEqualSkus)) {
            $arrayWithSku[self::CONTAINS] = array_unique($arrayWithEqualSkus);
        }
        if (!empty($arrayWithSku[self::EQUALS])
            || !empty($arrayWithSku[self::CONTAINS])
        ) {
            return $arrayWithSku;
        }
        return null;
    }
    /**
     * @param $observer
     */
    public function onLayoutGenerateBlocksAfter($observer)
    {
        if (!method_exists('Mage', 'getVersion')) {
            return;
        }
        if (version_compare(Mage::getVersion(), '1.9.3', '<')) {
            return;
        }
        $layout = $observer->getLayout();
        if (!in_array('ampromo_items', $layout->getUpdate()->getHandles())) {
            return;
        }
        $head = $layout->getBlock('head');
        $head->addJs('varien/product_options.js');
    }
}
