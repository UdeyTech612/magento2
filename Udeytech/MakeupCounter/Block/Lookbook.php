<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Block;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Lookbook
 * @package Udeytech\MakeupCounter\Block
 */
class Lookbook extends Template
{
    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    const LOOKBOOK_MEDIA_DIR = 'lookbook';
    /**
     * @var null
     */
    protected $_kitProduct = null;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var Image
     */
    protected $_imageHelper;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoriesCollection;
    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;
    /**
     * @var \Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory
     */
    protected $_lookbookCollection;
    /**
     * @var ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Lookbook constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param ProductRepository $productRepository
     * @param \Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory $lookbookCollection
     * @param ProductFactory $productFactory
     * @param Image $imageHelper
     * @param Registry $registry
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        ProductRepository $productRepository,
        \Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory $lookbookCollection,
        ProductFactory $productFactory,
        Image $imageHelper,
        Registry $registry,
        Data $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_productFactory = $productFactory;
        $this->_imageHelper = $imageHelper;
        $this->_registry = $registry;
        $this->productRepository = $productRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->_lookbookCollection = $lookbookCollection;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Get AssociatedProducts with hotspot points positions
     * @return array
     */
    public function getAssociatedProducts()
    {
        $result = $this->getAssociatedProductsCollection();
        foreach ($result as $item) {
            $item->setDayPositions($this->getItemPositions($item, Data::LOOKBOOK_TYPE_DAY));
            $item->setNightPositions($this->getItemPositions($item, Data::LOOKBOOK_TYPE_NIGHT));
        }
        return $result;
    }

    /**
     * Get AssociatedProducts Collection
     * @return array
     */
    public function getAssociatedProductsCollection()
    {
        $productData = $this->getProduct()->getTypeInstance()->getAssociatedProducts($this->getProduct());
        return $productData;
    }

    /**
     * Get Current Product
     * @return String
     */
    public function getProduct()
    {
        $objectManager = ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
        return $product;
    }

    /**
     * Get hotspot points position
     * @param $item Magento/Catalog/Model/Product
     * @param $type string
     * @return array|Udeytech/Lookbook/Model/Positions
     */
    public function getItemPositions($item, $type)
    {
        $itemId = $item->getId();
        $collection = $this->_lookbookCollection->create()->addFieldToFilter('parent_id', $this->getProduct()->getId())
            ->addFieldToFilter('simple_id', $itemId)->addFieldToFilter('type', $type)->getLastItem();
        return $collection;
    }

    /**
     * Get AssociatedProducts info to Html
     * @return string
     */
    public function getAssociatedProductsToHtml()
    {
        $collection = $this->getAssociatedProductsCollection();
        $associatedProductsHtml = '';
        foreach ($collection as $product) {
            $associatedProductsHtml .= $this->getChildBlock('product_info')->setProductId($product->getId())->toHtml();
        }
        return $associatedProductsHtml;
    }

    /**
     * Get closeups items
     * @param $type string
     * @return array|Varien_Data_Collection
     */
    public function getCloseups()
    {
        $product = $this->getProduct();
        $collection = $this->_collectionFactory->create();
        $closeups = $product->getData();
        if (!$closeups) return array();
        foreach ($closeups as $imageId => $closeup) {
            //$collection = new \Magento\Framework\DataObject();
            // if(!isset($closeup[$type])) continue;
            //  if ($closeup[$type] == 0 || $closeup[$type] == '0') continue;
            $item = $this->getGalleryImages()->getItemById($imageId);
            if (!$item) continue;
            $item->setData('associated_products', $closeup['associated_products']);
            $collection->addItem($item);
        }
        return $collection;
    }

    /**
     * Retrieve list of gallery images
     * @return array|Varien_Data_Collection
     */
    public function getGalleryImages()
    {
        return $this->getProduct()->getMediaGalleryImages();
    }

    /**
     * Get Look Image src by lokBook attribute
     * @param $lookAttr
     * @return string
     */
    public function getLookImgSrc($lookAttr)
    {
        if ($productImageAttr = $this->getProduct()->getCustomAttribute($lookAttr)) {
            $productImage = $this->_imageHelper->init($this->getProduct(), $lookAttr)->setImageFile($productImageAttr->getValue())->getUrl();
            return $productImage;
        }
    }

    /**
     * Get url link to next lookBook product from collection
     * @return string
     */
    public function getNextLookbookUrl()
    {
        $collection = $this->getLookbookCollection();
        $itemIds = $collection->getAllIds();
        $currentItemsId = $this->getProduct()->getId();
        $nextItemId = false;
        while (current($itemIds)) {
            if (current($itemIds) == $currentItemsId) {
                $nextItemId = next($itemIds);
                break;
            }
            next($itemIds);
        }
        if (!$nextItemId)
            $nextItemId = reset($itemIds);
        if ($nextItemId) {
            //$objectManager = ObjectManager::getInstance();
            //$product = $objectManager->create('Magento\Catalog\Model\Product')->load($nextItemId);
            //$url = $product->getProductUrl();
            $url = $collection->getItemById($nextItemId)->getProductUrl();
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * Get lookBook products collections
     * @return Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getLookbookCollection()
    {
        return $this->_helper->getLookbookCollection();
    }

    /**
     * Get url link to previous lookBook product from collection
     * @return string
     */
    public function getPrevLookbookUrl()
    {
        $collection = $this->getLookbookCollection();
        $itemIds = $collection->getAllIds();
        $currentItemsId = $this->getProduct()->getId();
        $prevItemId = false;
        while (current($itemIds)) {
            if (current($itemIds) == $currentItemsId) {
                $prevItemId = prev($itemIds);
                break;
            }
            next($itemIds);
        }
        if (!$prevItemId)
            $prevItemId = end($itemIds);
        if ($prevItemId) {
            //$objectManager = ObjectManager::getInstance();
            //$product = $objectManager->create('Magento\Catalog\Model\Product')->load($prevItemId);
            //$url = $product->getProductUrl();
            $url = $this->getLookbookCollection()->getItemById($prevItemId)->getProductUrl();
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * Get addToCart Url for related product kit
     * @return  string
     */
    public function getKitAddToCartUrl()
    {
        $kit = $this->getKitProduct();
        if ($kit->getId()) {
            return $this->getAddToCartUrl($kit);
        }
        return '';
    }

    /**
     * Get related kit product
     * @return mixed
     */
    public function getKitProduct()
    {
        if (is_null($this->_kitProduct)) {
            $sku = $this->getProduct()->getData('makeupcounter_kit');
            $this->_kitProduct = $this->productRepository->get($sku);
        }
        return $this->_kitProduct;
    }

    /**
     * Get kit product info to Html
     * @return string
     */
    public function getKitProductToHtml()
    {
        $product = $this->getKitProduct();
        $kitProductHtml = '';
        if (!$product->getId())
            return '';
        $kitProductHtml = $this->getChildBlock('product_info')->setProductId($product->getId())->toHtml();
        return $kitProductHtml;
    }
}
