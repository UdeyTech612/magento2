<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Productkit\Helper;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Currency;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Quote\Item;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Udeyetch\Productkit\Model\Expert;
use Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory;
use Udeytech\Productkit\Model\Source\Kit\Price;
/**
 * Class Data
 * @package Udeytech\Productkit\Helper
 */
class Data extends AbstractHelper {

    /**
     *
     */
    const FREE_KIT_SKU = 'product_kit_free';
    /**
     *
     */
    const PRODUCT_KIT_CUSTOM_ITEMS_COUNT = 'productkit_custom_kit_product_count';
    /**
     * @var bool
     */
    protected $_allKitsLoaded = false;
    /**
     * @var array
     */
    protected $_kits = array();
    /**
     * @var int
     */
    protected $_attributeSetId = 0;
    /**
     * @var string
     */
    protected $_prefix = 'product_kit_';
    /**
     * @var null
     */
    protected $_model = null;
    /** @var array $kitTypes */
    protected $kitTypes = array(1 => 'custom', 2 => 'free');
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
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopConfig;
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
     * @var Attribute
     */
    protected $eavAttribute;
    /**
     * @var Entity
     */
    protected $entity;
    /**
     * @var Currency
     */
    protected $_currency;
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @param Context $context
     */
    public function __construct(
            Context $context,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            ScopeConfigInterface $scopeConfig,
            StoreManagerInterface $storeManager,
            Session $customerSession,
            LoggerInterface $logger,
            Currency $currency,
            Attribute $eavAttribute,
            Entity $entity,
            CollectionFactory $collectionFactory,
            Config $eavConfig,
            RuleCollection $ruleCollection ){
            $this->_productCollectionFactory = $productCollectionFactory;
            $this->_scopConfig = $scopeConfig;
            $this->logger = $logger;
            $this->_customerSession = $customerSession;
            $this->_storeManager = $storeManager;
            $this->_ruleCollection = $ruleCollection;
            $this->eavAttribute = $eavAttribute;
            $this->entity = $entity;
            $this->eavConfig = $eavConfig;
            $this->_currency = $currency;
            $this->_collectionFactory = $collectionFactory;
            parent::__construct($context);
    }
    /**
     * Get text expert type
     * @param int $type
     * @return string
     */
    public function getKitTextType($type)
    {
        return $this->kitTypes[$type];
    }
    /**
     * @return string
     */
    public function getWizardUrlCustom()
    {
        return $this->_getUrl('productkit/mineralmakeup/custom');
    }
    /**
     * @return string
     */
    public function getWizardUrlFree()
    {
        return $this->_getUrl('productkit/mineralmakeup/free');
    }
    /**
     * @param $type
     * @return string
     */
    public function getWizardUrl($type)
    {
        return $this->_getUrl('productkit/mineralmakeup/' . $type);
    }
    /**
     * @param $product
     * @return mixed
     */
    public function getKitName($product)
    {
        if ($product instanceof Item) {
            $product = $product->getProduct();
        }
        if ($product->getKitName()) {
            return $product->getKitName();
        }
        return $this->getKitNameBySku($product->getSku());
    }
    /**
     * @param $sku
     * @return mixed
     */
    public function getKitNameBySku($sku)
    {
        return str_replace($this->_prefix, '', $sku);
    }
    /**
     * @param $sku
     * @return bool
     */
    public function isKitProduct($sku) {
    if ($sku instanceof \Magento\Quote\Model\Quote\Item) {
        $sku = $sku->getProduct();
    }
    if ($sku instanceof \Magento\Quote\Model\Quote\Item) {
        $sku = $sku->getSku();
    }
    if ($sku instanceof \Magento\Framework\DataObject) {
        $sku = $sku->getSku();
    }
    if (!is_string($sku)) {
        return false;
    }
    return 0 === strpos($sku, $this->_prefix);
    }
    /**
     * @param $name
     * @return mixed
     */
    public function getKitTypeFormated($name){
        $type = $this->getKitType($name);
        if (is_null($this->_model)) {
            $this->_model = $this->_->create('productkit/source_kit_type');
        }
        return $this->_model->getTypeByValues($type);
    }
    /**
     * @param $name
     * @return array|bool
     */
    public function getKitType($name)
    {
        $kit = $this->getKitByName($name);
        if ($kit) {
            return $kit->getData('productkit_kit_type');
        }
        return false;
    }
    /**
     * @param $name
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|mixed
     */
    public function getKitByName($name){
        if (isset($this->_kits[$name])) {
            return $this->_kits[$name];
        }
        $sku = $this->getKitSku($name);
        $model = $this->_productCollectionFactory->create();
        $id = $model->getIdBySku($sku);
        $kit = $this->_productCollectionFactory->create()->load($id);
        $this->updateKit($kit);
        $this->_kits[$kit->getKitName()] = $kit;
        return $kit;
    }
    /**
     * @param $name
     * @return string
     */
    public function getKitSku($name){
        return $this->_prefix . $name;
    }
    /**
     * @param $kit
     * @return bool
     */
    public function updateKit($kit){
        if (!$kit) {
            return false;
        }
        $kit->setKitName($this->getKitNameBySku($kit->getSku()));
    }
    /**
     * @return array
     */
    public function getAllKits(){
        if ($this->_allKitsLoaded) {
            return $this->_kits;
        }
        $products = $this->_productCollectionFactory->create();
        $products = $products->addAttributeToSelect('productkit_kit_type')
            ->addAttributeToSelect('productkit_kit_price')
            ->addAttributeToSelect('productkit_kit_value')
            ->addAttributeToSelect('productkit_kit_qty')
            ->addAttributeToSelect('productkit_kit_cms')
            ->addAttributeToSelect('kit_items_count')
            ->addAttributeToSelect('sku')
            ->addAttributeToFilter('attribute_set_id', $this->getAttributeSetId());
        foreach ($products as $product){
            $this->updateKit($product);
            $this->_kits[$product->getKitName()] = $product;
        }
        $this->_allKitsLoaded = true;
        return $this->_kits;
    }
    /**
     * @return int
     */
    public function getAttributeSetId(){
        if(!$this->_attributeSetId) {
            $model = $this->_productCollectionFactory->get();
            $entTypeId = $this->eavConfig->create()->getEntityType('catalog_product')->getId();
            $this->_attributeSetId = $model->getAttributeSetId($entTypeId, 'Product Kit');
        }
        return $this->_attributeSetId;
    }
    /**
     * @param float $price
     * @return float
     */
    public function preparePrice($price){
        return $this->_currency->format($price, array('display' => Zend_Currency::NO_SYMBOL), false, false);
    }
    /**
     * Get Kit Type
     *
     * @return mixed
     */
    public function _getKitType(){
        $attribute = $this->eavAttribute->getAttribute('catalog_product', 'productkit_type');
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        foreach ($options as $value) {
            if ($value['value'] != 0) {
                $optionsType[$value['value']] = $value['label'];
            }
        }
        return $optionsType;
    }
    /**
     * Create message if kit choose is not valid
     *
     * @param $data
     * @return bool|string
     */
    public function getErrorMessage($data)
    {
        if (array_key_exists('in_expert_choose', $data)) {

            $inExpertChoose = $data['in_expert_choose'];

            if (is_array($inExpertChoose)) {
                $selectedProducts = array_filter($inExpertChoose);
                $countSelectedProducts = count($selectedProducts);
                $productType = $data['kit_type'];
                $kitOptions = $this->getEnableKits();
                $countProductsForKit = (int)$kitOptions[$productType]['productkit_kit_qty'];

                $productCollection = $this->_productCollectionFactory->create()->getCollection()
                    ->addAttributeToSelect('productkit_type')
                    ->addAttributeToFilter('entity_id', array('in' => $selectedProducts))
                    ->load();

                foreach ($productCollection->getItems() as $productItem) {
                    if ($productItem['productkit_type'] != $productType) {
                        $itemNoValid[] = $productItem['sku'];
                    }
                }
            }
        }
        $errorMessage = false;
        return $errorMessage;
    }
    /**
     * Get options array of enable kids
     * @return mixed
     */
    public function getEnableKits(){
        $productsKitsData = $this->_productCollectionFactory->create();
        $productsKits = $productsKitsData->addAttributeToSelect('productkit_kit_type')
            ->addAttributeToSelect('productkit_kit_price')
            ->addAttributeToSelect('productkit_kit_value')
            ->addAttributeToSelect('productkit_kit_qty')
            ->addAttributeToSelect('productkit_kit_cms')
            ->addAttributeToSelect('kit_items_count')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('sku')
            ->addAttributeToFilter('status', array('eq' => array(1)))
            ->addAttributeToFilter('attribute_set_id', $this->getAttributeSetId());
        foreach ($productsKits as $kitOptionsArray) {
            $kitOptionsArray = $kitOptionsArray->getData();
            $optionsArray[(int)$kitOptionsArray['productkit_kit_type']] = $kitOptionsArray;
        }
        if ($optionsArray) {
            return $optionsArray;
        }
    }
    /**
     * Create message if kit has unavailable product
     * @param $model Expert
     * @return bool|string
     */
    public function getKitsWithUnavailableProducts($model){
        $productMessage = false;
        $selectedProducts = $model->getRecommendedProductIds();
        if (!isset($selectedProducts)) return $productMessage;
        if (count($selectedProducts) > 0) {

            foreach ($selectedProducts as $productId) {
                $product = $this->_productCollectionFactory->create()->load($productId);
                $stock = $product->joinField('cataloginventory/stock_item')->loadByProduct($product);
                $status = $product->getStatus();
                $statusStock = $stock->getIsInStock();
                $qty = $stock->getQty();
                if ((int)$statusStock !== 1 || (int)$status !== 1 || (int)$qty < 1) {
                    $productMessage .= 'Product "' . $product->getName() . '" is unavailable </br>';
                }
            }
        } else {
            $productMessage = "Please choose a product for the expert kit.";
        }
        return $productMessage;
    }
    /**
     * Update kit item price
     *
     * @param $kit Magento/Catalog/Model/Product
     * @param $originalPrice
     * @return mixed
     */
    public function getKitItemPrice($kit, $originalPrice){
        if (is_object($kit) && $kit->getProductkitKitPrice() > 0) {
            //percent discount
            if ($kit->getProductkitKitPrice() == Price::KIT_PRICE_PERCENT) {
                $originalPrice = $originalPrice * (1 - $kit->getProductkitKitValue() / 100);
            }
            //fixed discount should be counted to the whole set right now
        }
        return $originalPrice;
    }
    /**
     * @return
     */
    public function getKitCollection($product){
        $_kitType = $product->getProductkitKitType();
        $collection = $this->_productCollectionFactory->create();
        $collection = $this->_collectionFactory->create()->getCollection()
            ->addFieldToFilter('kit_type', $_kitType)
            ->addFieldToFilter('status', 1)
            ->validateChoosesProducts();
        return $collection;
    }
    /**
     * @return bool
     */
    public function isOnCheckoutPage(){
        $module = $this->getRequest()->getModuleName();
        return $module === 'onestepcheckout';
    }
}
