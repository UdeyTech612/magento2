<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSampleQuiz\Helper;
use Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\SaleRule\Model\Rule;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zend_Db_Select_Exception;

/**
 * Class Data
 * @package Udeytech\FreeSampleQuiz\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var bool
     */
    protected $_associatedCodes = false;
    /**
     * @var bool
     */
    protected $_freeColorSamplesCategoryCollection = false;
    /**
     * @var bool
     */
    protected $_baseCategoryCollection = false;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopConfig;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var RuleCollection
     */
    protected $_ruleCollection;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoriesCollection;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param CollectionFactory $productCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param RuleCollection $ruleCollection
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        CollectionFactory $productCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        RuleCollection $ruleCollection
    ){
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->_scopConfig = $scopeConfig;
        $this->logger = $logger;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_ruleCollection = $ruleCollection;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }
    /**
     * @return bool
     */
    public function isEnabled(){
        return true;
    }
    /**
     * @return string
     */
    public function getAssociatedCodesJSON(){
        $associatedCodes = $this->getAssociatedCodes();
        $codeAttributes = array_fill_keys(array_keys($associatedCodes), false);
        $codeItems = array();
        foreach ($associatedCodes as $attribut => $data) {
            foreach ($data['codes'] as $code) {
                if (!isset($codeItems[$code])) {
                    $codeItems[$code] = $codeAttributes;
                }
                $codeItems[$code][$attribut] = true;
            }
        }
        return $this->jsonHelper->jsonEncode($codeItems);
    }
    /**
     * @return array|bool
     */
    public function getAssociatedCodes(){
        if(!$this->_associatedCodes){
            $this->_associatedCodes = array(
                "Cool" => array(
                    "label" => __("Cool"),
                    "value" => "Cool",
                    "codes" => array(
                        "C0", "C1",
                        "C2", "C3",
                        "C4", "C5",
                        "C6", "C7",
                    )
                ),
                "Neutral" => array(
                    "label" => __("Neutral"),
                    "value" => "Neutral",
                    "codes" => array(
                        "N0", "N1",
                        "N2", "N3",
                        "N4", "N5",
                        "N6", "N7",
                    )
                ),
                "Warm" => array(
                    "label" => __("Warm"),
                    "value" => "Warm",
                    "codes" => array(
                        "W0", "W1",
                        "W2", "W3",
                        "W4", "W5",
                        "W6", "W7",
                    )
                ),
                "Fair" => array(
                    "label" => __("Fair"),
                    "value" => "Fair",
                    "codes" => array(
                        "C0", "C1",
                        "N0", "N1",
                        "W0", "W1",
                    )
                ),
                "Light" => array(
                    "label" => __("Light"),
                    "value" => "Light",
                    "codes" => array(
                        "C2", "C3",
                        "N2", "N3",
                        "W2", "W3",
                    )
                ),
                "Medium" => array(
                    "label" => __("Medium"),
                    "value" => "Medium",
                    "codes" => array(
                        "C4", "C5",
                        "N4", "N5",
                        "W4", "W5",
                    )
                ),
                "Tan" => array(
                    "label" => __("Tan"),
                    "value" => "Tan",
                    "codes" => array(
                        "C6", "C7",
                        "N6", "N7",
                        "W6", "W7",
                    )
                ),);
        }
        return $this->_associatedCodes;
    }
    /**
     * @param $codes
     * @return array|mixed
     */
    public function getProductsCodeByAssociatedCodes($codes){
        $result = array();
        $preparedCodes = $this->prepareCodes($codes);
        if(count($preparedCodes) > 0) {
            $result = $preparedCodes[0];
            foreach ($preparedCodes as $codes) {
                $result = array_intersect($result, $codes);
              }
            }
        return $result;
    }
    /**
     * @param $selectedAssociatedCodes
     * @return array
     */
    public function prepareCodes($selectedAssociatedCodes)
    {
        $result = array();
        if (is_array($selectedAssociatedCodes) && count($selectedAssociatedCodes) > 0) {
            $allCodes = $this->getAssociatedCodes();
            foreach ($selectedAssociatedCodes as $associatedCodes) {
                if (count($associatedCodes) > 0) {
                    $allCurrentCodes = array();
                    foreach ($associatedCodes as $associatedCode) {
                        if (array_key_exists($associatedCode, $allCodes)) {
                            $currentCodes = $allCodes[$associatedCode]["codes"];
                            $allCurrentCodes = array_merge($allCurrentCodes, $currentCodes);
                        }
                    }
                    $result[] = array_unique($allCurrentCodes);
                }
            }
        }

        return $result;
    }
    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getFreeColorSamplesCategoryCollection()
    {
        if ($this->_freeColorSamplesCategoryCollection === false) {
            $this->_freeColorSamplesCategoryCollection = $this->getCategoriesCollectionById(
                explode(',', $this->scopeConfig->getValue('freesamplequiz/general/free_sample_color_categories'))
            );
        }
        return $this->_freeColorSamplesCategoryCollection;
    }
    /**
     * @param $categoryIds
     * @return bool|\Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategoriesCollectionById($categoryIds)
    {
        try {
            $categoryCollection = $this->_categoriesCollection->create();
            $categoryCollection->addAttributeToFilter("entity_id", array("in" => $categoryIds));
            return $categoryCollection;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return false;
        }
    }
    /**
     * @return bool|Collection
     */
    public function getFormulaProductsFromCategories()
    {
        $categoryIds = $this->getBaseCategoryIdsByKeys();
        return $this->getProductsFromCategories($categoryIds);
    }
    /**
     * @return array
     */
    public function getBaseCategoryIdsByKeys()
    {
        return $this->getBaseCategoryCollection()->getAllIds();
    }
    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getBaseCategoryCollection()
    {
        if ($this->_baseCategoryCollection === false) {
            $this->_baseCategoryCollection = $this->getCategoriesCollectionById(
                explode(',', $this->_scopConfig->getValue('freesamplequiz/general/base_categories')));
        }
        return $this->_baseCategoryCollection;
    }
    /**
     * @param $categoryIds
     * @return bool|Collection
     * @throws LocalizedException
     */
    public function getProductsFromCategories($categoryIds)
    {
        if ($categoryIds) {
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect(array("name", "image", "short_description",));
            $collection->getSelect()->joinInner(
                array(
                    "category_product" => "catalog_category_product",
                ),
                "category_product.product_id = e.entity_id",
                array('category_product.position', 'GROUP_CONCAT(category_id) AS category_id'))->where("category_product.category_id IN (?)", $categoryIds)->order('position ASC');
            $collection->joinField('is_in_stock',
                'cataloginventory_stock_item',
                'is_in_stock',
                'product_id=entity_id',
                'is_in_stock=1',
                '{{table}}.stock_id=1',
                'left'
            );
            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addAttributeToFilter("is_saleable", "1")
                ->addAttributeToFilter("is_in_stock", "1")
                ->addAttributeToFilter('type_id', array('simple'))
                ->groupByAttribute("entity_id");
            return $collection;
        }
        return false;
    }
    /**
     * @param $fileName
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImgUrl($fileName)
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . "quiz/" . $fileName;
    }
    /**
     * @return bool|Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Db_Select_Exception
     */
    public function getProductsWithDiscount()
    {
        $productCollection = false;
        $productDiscounts = $this->getPriceRuleAssociatedProducts(\Udeytech\Productkit\Helper\Data::FREE_KIT_SKU);
        $productSkuArray = array_keys($productDiscounts);
        if ((bool)$productDiscounts) {
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addAttributeToSelect(array("name", "price", "image",));
            $productCollection->joinField(
                'is_in_stock',
                'cataloginventory_stock_item',
                'is_in_stock',
                'product_id=entity_id',
                'is_in_stock=1',
                '{{table}}.stock_id=1',
                'left'
            );
            $productCollection->addAttributeToFilter("sku", $productSkuArray);
            $productCollection->addAttributeToFilter("is_saleable", "1");
            $productCollection->addAttributeToFilter("is_in_stock", "1");
            $productCollection->load();
            if ($productCollection->getSize() > 0) {
                foreach ($productCollection as $product) {
                    if ($product->hasSku() && array_key_exists($product->getSku(), $productDiscounts)) {
                        $rules = $productDiscounts[$product->getSku()];
                        foreach ($rules as $rule) {
                            // rule format: type, amount
                            $ruleOption = explode(",", $rule);
                            $type = $ruleOption[0];
                            $amount = $ruleOption[1];
                            $discountedPrice = $product->hasDiscountedPrice() ? $product->getDiscountedPrice() : (float)$product->getPrice();
                            switch ($type) {
                                case Rule::BY_PERCENT_ACTION:
                                    $priceOff = $product->getPrice() * $amount / 100.0;
                                    $discountedPrice -= $priceOff;
                                    break;
                                case Rule::BY_FIXED_ACTION:
                                    $discountedPrice = $discountedPrice > $amount ? $discountedPrice - $amount : 0;
                                    break;
                            }
                            $product->setDiscountedPrice($discountedPrice);
                        }
                    }
                }
            }
        }
        return $productCollection;
    }
    /**
     * @param bool $productSku
     * @param string $attribute
     * @param string $couponCode
     * @return array
     * @throws NoSuchEntityException
     * @throws Zend_Db_Select_Exception
     */
    public function getPriceRuleAssociatedProducts($productSku = false, $attribute = 'sku', $couponCode = '')
    {
        $products = array();
        if ($productSku !== false) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            $customer = $this->_customerSession->getCustomer();
            $customerGroupId = ($customer) ? $customer->getGroupId() : 0;
            $collection = $this->_ruleCollection->create()->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->addFieldToFilter('main_table.conditions_serialized', array('like' => '%' . $productSku . '%'))
                ->addAttributeInConditionFilter($attribute)->addFieldToFilter('is_active', '1');
            $select = $collection->getSelect();
            //$select->reset(Zend_Db_Select::COLUMNS);
            $select->columns(array("CONCAT(main_table.simple_action, ',', main_table.discount_amount)", 'main_table.actions_serialized'));
            $items = $select->getAdapter()->fetchPairs($select);
            error_reporting(0);
            foreach ($items as $key => $val) {
                $data = unserialize($val);
                if (array_key_exists('conditions', $data)) {
                    $conditions = $data['conditions'];
                    foreach ($conditions as $condition) {
                        if (array_key_exists('attribute', $condition) && $condition['attribute'] == $attribute) {
                            $values = explode(',', $condition['value']);
                            foreach ($values as $value) {
                                $sku = trim($value);
                                if ($sku !== $productSku) {
                                    $products[$sku][] = $key;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        return $products;
    }
    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMediaUrl()
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
