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
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Udeytech\FreeSampleQuiz\Helper\Config;
use Udeytech\FreeSampleQuiz\Helper\Data;
use Udeytech\FreeSampleQuiz\Model\AnswersFactory;
use Udeytech\FreeSampleQuiz\Model\QuestionsFactory;
use Udeytech\Productkit\Model\Resource\Expert\Collection;

/**
 * Class Expertchoose
 * @package Udeytech\Productkit\Block\Wizard
 */
class Expertchoose extends Template
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
     * @var \Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory
     */
    protected $_productkitCollection;
    /**
     * @var Json
     */
    protected $_json;

    /**
     * Expertchoose constructor.
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
     * @param \Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory $productkitCollection
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
        StoreManagerInterface $storeManager,
        \Udeytech\Productkit\Helper\Data $productkitHelper,
        ScopeConfigInterface $scopeConfig,
        \Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory $productkitCollection,
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
        $this->_productkitCollection = $productkitCollection;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
        $this->_json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @return Atwix_Productkit_Model_Resource_Expert_Collection
     */
    public function getChooseCollection()
    {
        $_product = $this->getProduct();
        $_kitType = $_product->getProductkitKitType();
        /** @var Collection $collection */
        $collection = $this->_productkitCollection->create()->getCollection()
            ->addFieldToFilter('kit_type', $_kitType)
            ->addFieldToFilter('status', 1)
            ->validateChoosesProducts();
        return $collection;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }

    /**
     * Process wysiwyg text variables
     *
     * @param string $text
     * @return string
     */
    public function processText($text = '')
    {
        $_process = $this->cmsHelper->getBlockTemplateProcessor();
        return $_process->filter($text);
    }

    /**
     * Get product price and prepare html
     *
     * @param $id
     * @return bool|string
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
}


