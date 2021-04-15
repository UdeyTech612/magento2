<?php
/**
 * Copyright © udeytech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\MakeupCounter\Block\Adminhtml;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\MakeupCounter\Helper\Data;

class Product extends Template
{
    protected $productCollectionFactory;
    /**
     * Constructor
     *
     * @param Context  $context
     * @param array $data
     */
    protected $_imageHelper;
    protected $_registry;
    protected $_lookbookCollection;
    public function __construct(
        Context $context,
        Image $imageHelper,
        Registry $registry,
        \Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory $lookbookCollection,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_imageHelper = $imageHelper;
        $this->_registry = $registry;
        $this->_lookbookCollection = $lookbookCollection;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }
    /**
 * @param int $id
 * @return string
 */
    public function getTabLabel()
    {
        switch ($this->getId()) {
            case 'lookbook_day': return __('LookBook Day');
            case 'lookbook_night': return __('LookBook Night');
        }
        return $this->__('LookBook');
    }
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
    public function canShowTab()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() != 'grouped') {
            return FALSE;
        }
        try {
            $img = $this->_imageHelper->init($product, $this->_getLookBookAttribute())->setImageFile($product->getFile())->getUrl();
        } catch (Exception $e) {
            $img = FALSE;
        }
        return ($img)?TRUE:FALSE;
    }
    public function isHidden()
    {
        return FALSE;
    }
    public function getAssociatedProducts()
    {
        $result = $this->getProduct()->getTypeInstance(true)->getAssociatedProducts($this->getProduct());
        foreach ($result as $item) {
            $item->setPositions($this->getItemPositions($item));
        }
        return $result;
    }
    public function getItemPositions($item)
    {
        $itemId = $item->getEntityId();
        $collection = $this->_lookbookCollection->create()->getCollection()
            ->addFieldToFilter('parent_id', $this->getProduct()->getEntityId())
            ->addFieldToFilter('simple_id', $itemId)
            ->addFieldToFilter('type', $this->getLookBookType())
            ->getLastItem();
        return $collection;

    }
    public function getItemImage($item, $x = 54, $y = 54)
    {
        try {
            $img = $this->_imageHelper->init($item, $this->_getLookBookAttribute())->setImageFile($item->getFile())->resize($x, $y)->getUrl();
            } catch (Exception $e) {
            $img = FALSE;
         }
        return $img;
    }
    public function getLookBookImg()
    {
        return $this->_imageHelper->init($this->getProduct(), $this->_getLookBookAttribute());
    }
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }
    public function getLookBookType()
    {
        switch ($this->getId()) {
            case 'lookbook_day': return Data::LOOKBOOK_TYPE_DAY;
            case 'lookbook_night': return Data::LOOKBOOK_TYPE_NIGHT;
        }
        return  false;
    }
    protected function _getLookBookAttribute()
    {
        switch ($this->getId()) {
            case 'lookbook_day': return Data::LOOKBOOK_DAY_ATTR;
            case 'lookbook_night': return Data::LOOKBOOK_NIGHT_ATTR;
        }
        return  false;
    }
    public function getCloseupIndexUrl()
    {
        return $this->getUrl("adminhtml/closeups/index/");
    }
}

