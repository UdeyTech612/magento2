<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Block\Wizard;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Cms\Helper\Page;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\framework\Filter\FilterManager;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Udeytech\FreeSampleQuiz\Helper\Config;
use Udeytech\FreeSampleQuiz\Helper\Data;
use Udeytech\FreeSampleQuiz\Model\AnswersFactory;
use Udeytech\FreeSampleQuiz\Model\QuestionsFactory;

/**
 * Class Products
 * @package Udeytech\Productkit\Block\Wizard
 */
class Products extends Template
{
    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    protected $_freeColorSamplesByCategories;
    /**
     * @var
     */
    protected $_freeKitItems;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var QuestionsFactory
     */
    protected $_questionsFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var AnswersFactory
     */
    protected $_answersFactory;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var ResourceConnection
     */
    protected $_resource;
    /**
     * @var Attribute
     */
    protected $_eavAttribute;
    /**
     * @var \Udeytech\Productkit\Helper\Data
     */
    protected $_productkitHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoriesCollection;
    /**
     * @var Json
     */
    protected $_json;
    /**
     * @var FilterManager
     */
    protected $_filterManager;

    /**
     * Products constructor.
     * @param Context $context
     * @param Data $helper
     * @param Config $config
     * @param QuestionsFactory $questionsFactory
     * @param AnswersFactory $answersFactory
     * @param ResourceConnection $resource
     * @param Page $cmsHelper
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param \Udeytech\Productkit\Helper\Data $productkitHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param Attribute $eavAttribute
     * @param Json $json
     * @param FilterManager $filterManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Config $config,
        QuestionsFactory $questionsFactory,
        AnswersFactory $answersFactory,
        ResourceConnection $resource,
        Page $cmsHelper,
        Registry $registry,
        StoreManagerInterface $storeManager,
        \Udeytech\Productkit\Helper\Data $productkitHelper,
        ScopeConfigInterface $scopeConfig,
        Attribute $eavAttribute,
        Json $json,
        FilterManager $filterManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->_filterManager = $filterManager;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->_productkitHelper = $productkitHelper;
        $this->cmsHelper = $cmsHelper;
        $this->_eavAttribute = $eavAttribute;
        $this->_questionsFactory = $questionsFactory;
        $this->resource = $resource;
        $this->_answersFactory = $answersFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
        $this->_json = $json;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve loaded category collection
     * @return /Magento/Eav/Model/Entity/Collection/Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $category = $this->getCurrentCategory();
            $collection = $this->_productCollectionFactory->create()
                ->getCollection();
            $collection->setStoreId($category->getStoreId())
                ->getSelect()->joinLeft(
                    array('category_product' => $this->resource->getTableName('catalog/category_product')),
                    'category_product.product_id=entity_id',
                    array('category_id' => 'category_product.category_id')
                )->where('category_id = ' . $category->getId());
            $this->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @param $collection
     * @return $this
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect($this->_eavAttribute->getProductAttributes())
            ->addAttributeToFilter('productkit_type', $this->getProduct()->getData('productkit_kit_type'))
            ->addAttributeToFilter('attribute_set_id', array('nin' => Mage::helper('productkit')->getAttributeSetId()))
            ->addUrlRewrite($this->getCurrentCategory()->getId());
        $request = $this->getRequest();
        $excludeIds = $request->getParam('except', array());
        if ($excludeIds) {
            $collection->addAttributeToFilter('entity_id', array('nin' => $excludeIds));
        }
        $collection->addField('cataloginventory/stock_status')->addIsInStockFilterToCollection($collection);
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }

    /**
     * @param unknown_type $product
     * @return array
     */
    public function getFormatedDescription($product)
    {
        // truncate standard view
        $result = array();
        /* @var $product /Magento/Catalog/Model/Product */
        $_sourceText = $product->getShortDescription();
        $descriptionValue = strip_tags($_sourceText);
        $_truncatedValue = $this->filterManager->truncate($descriptionValue, 55, '');
        $_truncatedValue = nl2br($_truncatedValue);
        $result = array('value' => $_truncatedValue);
        if ($this->filterManager->strlen($descriptionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $descriptionValue = nl2br($_sourceText);
            $result = array_merge($result, array('full_view' => $descriptionValue));
        }
        return $result;
    }

    /**
     * @param $product
     * @return string
     */
    public function getPriceHtml($product)
    {
        $this->setTemplate('productkit/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }

    /**
     * @param $originalPrice
     */
    public function getKitItemPrice($originalPrice)
    {

    }

    /**
     * @return $this|Template
     */
    protected function _beforeToHtml()
    {
        // skip toolbar initialization
        return $this;
    }
}

