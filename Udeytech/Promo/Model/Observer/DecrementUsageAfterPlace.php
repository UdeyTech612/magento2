<?php
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class DecrementUsageAfterPlace
 * @package Udeytech\Promo\Model\Observer
 */
class DecrementUsageAfterPlace {
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $_stockFilter;
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * DecrementUsageAfterPlace constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Helper\Stock $stockFilter
     * @param \Magento\Framework\ObjectManagerInterface $_objectManager
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\Auth $auth
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth $auth
    ){
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
        $this->_auth = $auth;
        $this->ruleFactory = $ruleFactory;
        $this->_objectManager = $_objectManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        }
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        if ($ruleIds && $this->_auth->isLoggedIn()) {
            $this->_setItemPrefix($order->getAllItems());
        }
        $ruleCustomer = null;
        $customerId = $order->getCustomerId();
        if(($order->getDiscountAmount() == 0) && (count($ruleIds) >= 1)) {
            foreach ($ruleIds as $ruleId) {
                if (!$ruleId) {
                    continue;
                }
                $rule = $this->ruleFactory->create();
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
            $coupon = $this->ruleFactory->create();
            $coupon->load($order->getCouponCode(), 'code');
            if ($coupon->getId()) {
                $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
                $coupon->save();
                if ($customerId) {
                    $couponUsage = $this->ruleFactory->create();
                    $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
                }
            }
        }
    }

    /**
     * @param $items
     */
    protected function _setItemPrefix($items){
        $prefix = $this->_scopeConfig->getValue('ampromo/general/prefix');
        foreach ($items as $item) {
            $buyRequest = $item->getBuyRequest();
            $labelName  = $item->getLabelName();
            if (isset($buyRequest['options']['ampromo_rule_id'])) {
                $rule = $this->_loadRule($buyRequest['options']['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
            } elseif (isset($buyRequest['ampromo_rule_id']) && !isset($labelName)
            ) {
                $rule = $this->_loadRule($buyRequest['ampromo_rule_id']);
                $this->_generateNameWithLabel($rule, $item, $prefix);
                $item->setLabelName(1);
            }
        }
    }
}

