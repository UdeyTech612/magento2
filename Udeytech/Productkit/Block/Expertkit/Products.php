<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSampleQuiz\Block\Expertkit;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Cms\Helper\Page;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\FreeSampleQuiz\Helper\Config;
use Udeytech\FreeSampleQuiz\Helper\Data;
use Udeytech\FreeSampleQuiz\Model\AnswersFactory;
use Udeytech\FreeSampleQuiz\Model\QuestionsFactory;

/**
 * Class Products
 * @package Udeytech\FreeSampleQuiz\Block\Expertkit
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
     * Products constructor.
     * @param Context $context
     * @param Data $helper
     * @param Config $config
     * @param QuestionsFactory $questionsFactory
     * @param AnswersFactory $answersFactory
     * @param ResourceConnection $resource
     * @param Page $cmsHelper
     * @param Registry $registry
     * @param \Udeytech\Productkit\Helper\Data $productkitHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param Attribute $eavAttribute
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
        \Udeytech\Productkit\Helper\Data $productkitHelper,
        ScopeConfigInterface $scopeConfig,
        Attribute $eavAttribute,
        CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->_productkitHelper = $productkitHelper;
        $this->cmsHelper = $cmsHelper;
        $this->_eavAttribute = $eavAttribute;
        $this->_questionsFactory = $questionsFactory;
        $this->resource = $resource;
        $this->_answersFactory = $answersFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * @param $_product
     * @return bool
     */
    public function prepareProductKitPrice($_product)
    {
        $productBlockObject = new Product;
        $priceHtml = $productBlockObject->getPriceHtml($_product);
        if ($priceHtml) {
            return $priceHtml;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * @return mixed
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $category = $this->getCurrentCategory();
            $collection = $this->_productCollectionFactory->create()->getCollection();
            $collection->setStoreId($category->getStoreId())
                ->getSelect()->joinLeft(
                    array('category_product' => $this->_resource->getTableName('catalog/category_product')),
                    'category_product.product_id=entity_id',
                    array(
                        'category_id' => 'category_product.category_id',
                        'category_product.position',
                    ))->where('category_id = ' . $category->getId());
            $collection->getSelect()->order('position ' . Varien_Data_Collection::SORT_ORDER_ASC);
            $this->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @return Category
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
        $collection->addAttributeToSelect($this->_eavAttribute->getProductAttributes());
        $collection->addAttributeToFilter('productkit_type', $this->getProduct()->getData('productkit_kit_type'));
        $collection->addAttributeToFilter('attribute_set_id', array('nin' => $this->_productkitHelper->getAttributeSetId()));
        $collection->addUrlRewrite($this->getCurrentCategory()->getId());
        $request = $this->getRequest();
        $excludeIds = $request->getParam('except', array());
        if ($excludeIds) {
            $collection->addAttributeToFilter('entity_id', array('nin' => $excludeIds));
        }
        $collection->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
        )->addFieldToFilter('stock_status', array('eq' => Status::STATUS_IN_STOCK));
        return $this;
    }

    /**
     * Get category position in the product kit items
     *
     * @return int
     */
    public function getCurrentCategoryPosition()
    {
        return $this->_registry->registry('current_category_position');
    }

    /**
     * @return int
     */
    public function getCurrentKitId()
    {
        return $this->_registry->registry('current_kit_id');
    }
}

