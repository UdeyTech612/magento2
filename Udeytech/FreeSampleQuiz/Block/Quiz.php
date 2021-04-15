<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\FreeSampleQuiz\Block;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Cms\Helper\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\FreeSampleQuiz\Helper\Config;
use Udeytech\FreeSampleQuiz\Helper\Data;
use Udeytech\FreeSampleQuiz\Model\AnswersFactory;
use Udeytech\FreeSampleQuiz\Model\QuestionsFactory;

/**
 * Class Quiz
 * @package Udeytech\FreeSampleQuiz\Block
 */
class Quiz extends Template
{
    /**
     * @var
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
     * Quiz constructor.
     * @param Context $context
     * @param Data $helper
     * @param Config $config
     * @param QuestionsFactory $questionsFactory
     * @param AnswersFactory $answersFactory
     * @param ResourceConnection $resource
     * @param Page $cmsHelper
     * @param ScopeConfigInterface $scopeConfig
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
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ){
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->cmsHelper = $cmsHelper;
        $this->_questionsFactory = $questionsFactory;
        $this->resource = $resource;
        $this->_answersFactory = $answersFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }
    /**
    * @return bool|\Magento\Catalog\Model\ResourceModel\Product\Collection
    */
    public function getFormulaProductsByCategories()
    {
    return $this->helper->getFormulaProductsFromCategories();
    }
    /**
    * @return string
    */
    public function getAssociatedCodesJSON()
    {
    return $this->helper->getAssociatedCodesJSON();
    }
    /**
    * @return bool|\Magento\Catalog\Model\ResourceModel\Product\Collection
    */
    public function getBrushesWithDiscount()
    {
    $prodcutdata = $this->helper->getProductsWithDiscount();
    return $prodcutdata;
    }
    /**
    * @return bool|Collection
    * @throws LocalizedException
    */
    public function getBasesCategoryCollection() {
    $collection = $this->helper->getBaseCategoryCollection();
    $collection->addAttributeToSelect(array("name", "description"));
    return $collection;
    }
    /**
    * @return array
    * @throws LocalizedException
    */
    public function getFreeColorSamplesByCategories() {
    if (is_null($this->_freeColorSamplesByCategories)) {
        $samples = array();
        foreach($this->_getFreeColorSamplesCategoryCollection() as $category) {
            $products = $this->_getProductCollectionByCategory($category)->addAttributeToFilter('sku', array('neq' => 'product_kit_free'));
            $samples[$category->getId()] = array('category_name' => $category->getName(),
                'samples' => $products);
        }
        $this->_freeColorSamplesByCategories = $samples;
    }
    return $this->_freeColorSamplesByCategories;
    }
    /**
    * @return bool|Collection
    * @throws LocalizedException
    */
    public function _getFreeColorSamplesCategoryCollection()
    {
    $collection = $this->helper->getFreeColorSamplesCategoryCollection();
    $collection->addAttributeToSelect('name');
    return $collection;
    }
    /**
    * @param $category
    * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
    * @throws LocalizedException
    */
    public function _getProductCollectionByCategory($category)
    {
    $collection = $this->_productCollectionFactory->create();
    $connection = $this->resource->getConnection();
    //      $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    $collection->getSelect()->joinLeft(array('category_product' => $connection->getTableName('catalog_category_product')),
        'category_product.product_id=e.entity_id', array('category_id' => 'category_product.category_id', 'category_product.position',)
    )->where('category_id = ' . $category->getId());
    $collection->joinField('is_in_stock',
        'cataloginventory_stock_item',
        'is_in_stock',
        'product_id=entity_id',
        'is_in_stock=1',
        '{{table}}.stock_id=1',
        'left'
    )->addAttributeToFilter("is_in_stock", "1")->joinField(
        'qty',
        'cataloginventory_stock_item',
        'qty',
        'product_id=entity_id',
        '{{table}}.stock_id=1',
        'left'
    )->addAttributeToFilter('qty', array('gt' => array(1)));
    //$collection->getSelect()->order('position ', \Magento\Framework\DataObject::SORT_ORDER_ASC)->load();
    return $collection;
    }
    /**
    * @return array
    * @throws LocalizedException
    */
    public function getFreeKitItems()
    {
    if (!$this->_freeKitItems) {
        if (is_null($this->_freeKitItems)) {
            $this->_freeKitItems = array();
            $categoryList = array();
            foreach ($this->_getBaseColorCategoryCollection() as $category) {
                $products = $this->helper->getProductsFromCategories($category->getId())->addAttributeToFilter('sku', array('neq' => 'product_kit_free'));
                $categoryListItem = array('category' => $category, 'products' => $products);
                array_push($categoryList, $categoryListItem);
            }
            $freekitItem = array('category_list' => $categoryList, 'item_name' => 'Base', 'products' => false);
            array_push($this->_freeKitItems, $freekitItem);
            $categoryList = array_map(function ($items) {
                $items['products'] = false;
                return $items;
            }, $categoryList);
            $freekitItem = array(
                'category_list' => $categoryList,
                'item_name' => 'Base',
                'products' => false
            );
            array_push($this->_freeKitItems, $freekitItem);
            foreach ($this->_getFreeColorSamplesCategoryCollection() as $category) {
                $products = $this->helper->getProductsFromCategories($category->getId())->addAttributeToFilter('sku', array('neq' => 'product_kit_free'));
                $freekitItem = array('category_list' => false, 'item_name' => $category->getName(), 'products' => $products);
                array_push($this->_freeKitItems, $freekitItem);
            }
        }
    }
    return $this->_freeKitItems;
    }
    /**
    * @return bool|Collection
    * @throws LocalizedException
    */
    public function _getBaseColorCategoryCollection()
    {
    $collection = $this->helper->getBaseCategoryCollection();
    $collection->addAttributeToSelect(array('name'));
    return $collection;
    }
    /**
    * @return AbstractCollection
    */
    public function getQuestions()
    {
    return $this->_questionsFactory->create()->getCollection();
    }
    /**
    * @param $questionId
    * @return AbstractCollection
    */
    public function getAnswerCollection($questionId)
    {
    $answerCollection = $this->_answersFactory->create()->getCollection();
    $answerCollection->addFieldToFilter('questions_id', array('eq' => $questionId));
    //$answerCollection = $answerCollection->addAttributeToFilter('questions_id','1');
    return $answerCollection;
    }
    /**
    * @return string
    */
    public function getAddToCartUrl()
    {
    return $this->getUrl('productkit/mineralmakeup/addtocart');
    }
    /**
    * @return string
    */
    public function getMakeupcounterUrl()
    {
    return $this->getUrl('makeupcounter');
    }
    /**
    * @return string
    */
    public function isFreeKitDuplicateCheckUrl()
    {
    return $this->getUrl('productkit/mineralmakeup/isfreekitduplicate');
    }
    /**
    * @return string
    */
    public function getSkipUrl()
    {
    $freeKitCmsPage = $this->scopeConfig->getValue('freesamplequiz/general/cms_freekit_page');
    return $this->cmsHelper->getPageUrl($freeKitCmsPage);
    }
}

