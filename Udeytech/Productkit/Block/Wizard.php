<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Block;

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

/**
 * Class Wizard
 * @package Udeytech\Productkit\Block
 */
class Wizard extends Template
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
     * Wizard constructor.
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
        $this->_storeManager = $storeManager;
        $this->_json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getKitType()
    {
        $type = $this->_helper()->getKitTypeFormated($this->getKitName());
        return $type;
    }

    /**
     * @return mixed
     */
    public function getKitName()
    {
        $sku = $this->getProduct()->getSku();
        $type = $this->_helper()->getKitNameBySku($sku);
        return $type;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return parent::getProduct();
    }

    /**
     * @return mixed
     */
    public function getKitProductsQty()
    {
        return $this->getProduct()->getData('productkit_kit_qty');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesSelectHtml()
    {
        $categories = $this->getKitCategoriesCollection();
        $htmlId = 'placeholder_select_category_{{idx}}';
        $categoriesOptions = $this->getKitCategoriesList();
        $select = $this->getLayout()->createBlock('core/html_select');
        /* @var $select Mage_Core_Block_Html_Select */
        $select->setData(array('id' => $htmlId, 'class' => 'select select-placeholder-category'));
        $select->setOptions($categoriesOptions);
        if ($categories->getSize() == 1) {
            $select->setValue($categories->getFirstItem()->getId());
        }
        return $select->getHtml();
    }

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getKitCategoriesCollection()
    {
        $collection = $this->getProduct()->getCategoryCollection()
            ->addNameToResult()->addFieldToFilter('attribute_set_id',
                array('nin' => $this->_helper()->getAttributeSetId()))
            ->addIsActiveFilter();
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getKitCategoriesList()
    {
        $key = $this->getKitName() . '_categories_list';
        if (!$this->hasData($key)) {
            $cats = $this->getKitCategoriesCollection();
            $return = array();
            $return[] = array('value' => '', 'label' => __('-- please select --'),);
            foreach ($cats as $_cat) {
                $return[] = array(
                    'value' => $_cat->getId(),
                    'label' => $_cat->getName(),
                );
            }
            $this->setData($key, $return);
        }
        return $this->getData($key);
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
    public function getExpertUrl()
    {
        return $this->getUrl('*/*/experts');
    }

    /**
     * @return string
     */
    public function getWizardPostUrl()
    {
        if ($this->isWizardEditMode()) {
            return $this->getUrl('*/*/configure', array('id' => $this->getKitQuoteItem()->getId()));
        }
        return $this->getUrl('*/*/addtocart');
    }

    /**
     * @return bool
     */
    public function isWizardEditMode()
    {
        if ($this->getKitQuoteItem() && $this->getKitQuoteItem()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getKitQuoteItem()
    {
        if (!$this->hasData('kit_quote_item')) {
            $this->setData('kit_quote_item', $this->_registry->registry('kit_quote_item'));
        }
        return $this->getData('kit_quote_item');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWizardKitPrice()
    {
        if ($this->isWizardEditMode()) {
            $price = $this->getKitQuoteItem()->getPrice();
            return $this->_formatKitPrice($price);
        }
        return $this->getFreeKitMinPrice();
    }

    /**
     * @param $price
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _formatKitPrice($price)
    {
        return $this->_storeManager->getStore()->getDefaultCurrency()->format($price, array('display' => Zend_Currency::NO_SYMBOL), false, false);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFreeKitMinPrice()
    {
        $price = $this->getProduct()->getData('productkit_kit_value');
        return $this->_formatKitPrice($price);
    }

    /**
     * @return int
     */
    public function getWizardKitQty()
    {
        if ($this->isWizardEditMode()) {
            return $this->getKitQuoteItem()->getQty();
        }
        return 1;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getCartButtonLabel()
    {
        if ($this->isWizardEditMode()) {
            return __('Update Cart');
        }

        return __('+ ADD TO BASKET');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getKitCmsBlockHtml()
    {
        $type = $this->getKitName();
        if (!$this->getData($type . '_cms_block_html')) {
            if ($this->getProduct()->getData('productkit_kit_cms')) {
                $html = $this->getLayout()->createBlock('Magento\Framework\Cms\Block\Block')
                    ->setBlockId($this->getProduct()->getData('productkit_kit_cms'))
                    ->toHtml();
                $this->setData($type . '_cms_block_html', $html);
            }
        }
        return $this->getData($type . '_cms_block_html');
    }
}

