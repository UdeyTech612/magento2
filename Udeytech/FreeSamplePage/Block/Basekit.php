<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\FreeSamplePage\Block;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Udeytech\FreeSamplePage\Helper\Data;

/**
 * Class Basekit
 * @package Udeytech\FreeSamplePage\Block
 */
class Basekit extends Template {
    /**
     * @var null
     */
    protected $_rootCategory = null;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoriesCollection;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var ProductFactory
     */
    protected $_productFactory;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var PriceHelper
     */
    protected $priceHelper;
    /**
     * @var array
     */
    protected $_facesNameByGroup = array('1' => array(
        'group_name' => 'Cool',
        'codes' => array(
            '1' => '0C-1C',
            '4' => '2C-3C',
            '7' => '4C-5C',
            '10' => '6C-7C'
        )
    ), '2' => array(
        'group_name' => 'Neutral',
        'codes' => array(
            '2' => '0N-1N',
            '5' => '2N-3N',
            '8' => '4N-5N',
            '11' => '6N-7N'
        )
    ), '3' => array(
        'group_name' => 'Warm',
        'codes' => array(
            '3' => '0W-1W',
            '6' => '2W-3W',
            '9' => '4W-5W',
            '12' => '6W-7W'
        )
    ));
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        CollectionFactory $productCollectionFactory,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        ResourceConnection $resourceConnection,
        ProductFactory $productFactory,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        Data $helper,
        PriceHelper $priceHelper,
        array $data = []
    ){
        $this->productRepository = $productRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->_registry = $registry;
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->data = $data;
        $this->resourceConnection = $resourceConnection;
        $this->priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }
    /**
     * @return Template
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    /**
     * Get Basekit faces
     * @return array
     */
    public function getFacesByGroup()
    {
        $baseKit = $this->_productFactory->create()->load($this->getProduct()->getId());
        $imagesCollection = $baseKit->getMediaGalleryImages();
        $faceItems = array();
        foreach ($imagesCollection as $key => $image) {
            foreach ($this->_facesNameByGroup as $groupKey => $group) {
                (!isset($faceItems[$groupKey]['group_name'])) ? $faceItems[$groupKey]['group_name'] = $group['group_name'] : true;
                (!isset($faceItems[$groupKey]['images'])) ? $faceItems[$groupKey]['images'] = array() : true;
                if (in_array(strtoupper($image->getLabel()), $group['codes'])) {
                    $facePosition = array_search(strtoupper($image->getLabel()), $group['codes']);
                    $faceItems[$groupKey]['images'][$facePosition] = $image;
                }
                ksort($faceItems[$groupKey]['images']);
            }
        }
        ksort($faceItems);
        return $faceItems;
    }
    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }
    /**
     * Return an array of bundle product options with categories
     * @return array
     */
    public function getBasekitItems()
    {
        $objectManager = ObjectManager::getInstance();
        $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
        $product = $productRepository->getById($this->getProduct()->getId());
        $store_id = $this->_storeManager->getStore()->getId();
        $options = $objectManager->get('Magento\Bundle\Model\Option')->getResourceCollection()->setProductIdFilter($product->getId())->setPositionOrder();
        $options->joinValues($store_id);
        $typeInstance = $objectManager->get('Magento\Bundle\Model\Product\Type');
        $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
        foreach ($options as $item) {
            $categoryList = array();
            foreach ($selections as $producta) {
                if ($item->getOptionId() == $producta->getOptionId()) {
                    $categoryItems = $this->_getProductCategoryItems($producta);
                    foreach ($categoryItems as $category) {
                        $id = $category['category_id'];
                        if (!isset($categoryList[$id])) {
                            $categoryList[$id] = array('category_id' => $category['category_id'], 'category_name' => $category['category_name'], 'product_ids' => array());
                        }
                        array_push($categoryList[$id]['product_ids'], $category['product_id']);
                     }
                  }
               }
            $item->setData('category_list', $categoryList);
          }
        $items = $options;
        return $items;
    }
    /**
     * @param $product
     * @return array
     */
    protected function _getProductCategoryItems($product){
    $rootCategoryId = $this->_getRootCategory()->getId();
    $categoryCollection = $product->getCategoryCollection()
        ->addAttributeToFilter('parent_id', $rootCategoryId)
        ->addAttributeToFilter('is_active', 1)
        ->addAttributeToSelect('*');
        $categoryItems = array();
    foreach($categoryCollection as $category){

    $categoryItems[] = array(
       'category_id' => $category->getId(),
       'category_name' => $category->getName(),
       'product_id' => $category->getProductId());
        }
        return $categoryItems;
    }
    /**
     * @return |null
     */
    protected function _getRootCategory(){
        if (is_null($this->_rootCategory)) {
            $this->_rootCategory = ObjectManager::getInstance()->create(Category::class)->loadByAttribute('url_key', 'free-samples');
        }
        return $this->_rootCategory;
    }
    /**
     * Get selection product from bundle option
     * @param $optionKey
     * @param $productId
     * @return bool
     */
    public function getProductByIdFromOptionByKey($optionKey, $productId){
        $_objectManager = ObjectManager::getInstance();
        $product = $this->_productFactory->create()->load($this->getProduct()->getId());
        $typeInstance = $_objectManager->get('Magento\Bundle\Model\Product\Type');
        $selectionss = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);
        $selections = $options[$optionKey] = $selectionss;
        foreach ($selections as $product){
            if ($product->getId() == $productId)
                return $product;
        }
        return false;
    }
    /**
     * @param $product
     * @param array $additional
     * @return mixed
     */
    public function getAddToCartUrl($product, $additional = array()){
        // $productViewBlock = $this->getLayout()->createBlock('catalog/product_view');
        // if (!is_null($productViewBlock) && $productViewBlock instanceof View) {
        //     return $productViewBlock->getAddToCartUrl($product, $additional = array());
        // }
        return parent::getAddToCartUrl($product, $additional = array());
    }
    /**
     * @param null $productId
     * @return mixed
     */
    public function setFspBundleProduct($productId = null){
        $_product = $this->_helper->getFspBundleProduct($productId);
        $this->_registry->registry('product', $_product);
        return $_product;
    }
    /**
     * @param null $productId
     * @return mixed
     */
    public function setFspBundleProduct1($productId = null)
    {
        $_product = $this->_helper->getFspBundleProduct1($productId);
        $this->_registry->registry('product', $_product);
        return $_product;
    }
}
