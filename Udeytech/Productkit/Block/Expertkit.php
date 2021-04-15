<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Cms\Helper\Page;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\FreeSampleQuiz\Helper\Config;
use Udeytech\FreeSampleQuiz\Helper\Data;
use Udeytech\FreeSampleQuiz\Model\AnswersFactory;
use Udeytech\FreeSampleQuiz\Model\QuestionsFactory;

/**
 * Class Expertkit
 * @package Udeytech\Productkit\Block
 */
class Expertkit extends Template
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
     * Expertkit constructor.
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
     * @param Json $json
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
        \Udeytech\Productkit\Helper\Data $productkitHelper,
        ScopeConfigInterface $scopeConfig,
        Attribute $eavAttribute,
        Json $json,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
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
        $this->_categoriesCollection = $categoriesCollection;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->_json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @return integer
     */
    public function getDefaultKitId()
    {
        return $this->_registry->registry('current_expert_kit_id');
    }

    /**
     * @return string JSON
     */
    public function getJsonExpertKitCollection()
    {
        $collection = $this->getExpertKitCollection();
        $expertKitCollection = array();
        foreach ($collection as $kit) {
            $expertKitCollection[$kit->getData('choose_id')] = array(
                'id' => $kit->getData('choose_id'),
                'title' => $kit->getData('kit_choose_title'),
                'description' => $kit->getData('description'),
                'products' => $kit->getExpertProducts(),
                'items' => $kit->getExpertItems(),
                'allCategories' => $kit->getAllCategories(),
                'amount' => count($kit->getExpertProducts()) + count($kit->getExpertItems())
            );
        }
        return $this->_json->serialize($expertKitCollection);
    }

    /**
     * @return array
     */
    public function getExpertKitCollection()
    {
        $product = $this->getProduct();
        $collection = $this->_helper()->getKitCollection($product);
        $expertKits = array();
        /** @var Atwix_Productkit_Model_Expert $kit */
        foreach ($collection as $id => $kit) {
            $expertProducts = $kit->getRecommendedProductIds();
            $productCollection = $this->_productCollectionFactory->create()->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $expertProducts));
            $collection->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            )->addFieldToFilter('stock_status', array('eq' => Status::STATUS_IN_STOCK));
            // we won't show kit if it has out of stock products in the selected items
            if ($productCollection->getSize() < count($expertProducts)) {
                continue;
            }
            $expertItems = $kit->getItemsModel()->getAllItems();
            $allCategories = $kit->getItemsModel()->getAllCategories();
            $kit->setExpertProducts($expertProducts);
            $kit->setExpertItems($expertItems);
            $kit->setAllCategories($allCategories);
            $expertKits[$id] = $kit;
        }
        return $expertKits;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }

    /**
     * @param $categoriesList string
     * @return mixed
     */
    public function getCollectionByCategories($categoriesList)
    {
        $collection = $this->_categoriesCollection->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', array("in" => $categoriesList))
            ->addIsActiveFilter()
            ->addAttributeToSelect('*');
        $collection = $this->_hideEmptyCategories($collection);
        return $collection;
    }

    /**
     * Remove empty categories
     * @param   $collection     Mage_Catalog_Model_Resource_Category_Flat_Collection
     * @return  Mage_Catalog_Model_Resource_Category_Flat_Collection
     */
    protected function _hideEmptyCategories($collection)
    {
        foreach ($collection as $key => $item) {
            if ($item->getProductCount() < 1) {
                $collection->removeItemByKey($key);
            }
        }
        return $collection;
    }

    /**
     * @return string
     */
    public function getProductListUrl()
    {
        return $this->getUrl('*/*/products');
    }

    /**
     * @return string
     */
    public function getAddToCartUrl()
    {
        return $this->getUrl('*/*/addtocart');
    }
}

