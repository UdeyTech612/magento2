<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Lookbook\Block\Adminhtml\Product\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;
use Psr\Log\LoggerInterface;
use Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class CustomData
 * @package Udeytech\Lookbook\Block\Adminhtml\Product\Edit\Tab
 */
class CustomData extends Template
{
    /**
     * @var string
     */
    protected $_template = 'lookbook/product_tab_form.phtml';
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var Data
     */
    protected $_helperData;
    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * CustomData constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param LoggerInterface $logger
     * @param Image $imageHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        LoggerInterface $logger,
        Image $imageHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_helperData = $helperData;
        $this->_imageHelper = $imageHelper;
        $this->logger = $logger;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        switch ($this->getId()) {
            case 'lookbook_day':
                return __('LookBook Day');
            case 'lookbook_night':
                return __('LookBook Night');
        }
        return __('LookBook');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() != 'grouped') {
            return FALSE;
        }
        try {
            $imageUrl = $this->_imageHelper->init($product, $this->_getLookBookAttribute())
                ->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->getUrl();
        } catch (Exception $e) {
            $imageUrl = FALSE;
        }
        return ($imageUrl) ? TRUE : FALSE;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return bool|string
     */
    protected function _getLookBookAttribute()
    {
        switch ($this->getId()) {
            case 'lookbook_day':
                return Data::LOOKBOOK_DAY_ATTR;
            case 'lookbook_night':
                return Data::LOOKBOOK_NIGHT_ATTR;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return FALSE;
    }

    /**
     * @return mixed
     */
    public function getAssociatedProducts()
    {
        $positions = $this->_collectionFactory->create()->load()->addFieldToFilter('parent_id', $this->getProduct()->getId());
        $associatedProducts = $this->getProduct()->getTypeInstance()->getAssociatedProducts($this->getProduct());
        foreach ($associatedProducts as $product) {
            $product->setPositions($this->getItemPositions($product->getId()));
        }
        return $associatedProducts;
    }

    /**
     * @param $item_id
     * @return mixed
     */
    public function getItemPositions($item_id)
    {
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('parent_id', $this->getProduct()->getId())
            ->addFieldToFilter('simple_id', $item_id)
            ->addFieldToFilter('type', 'day')->getLastItem();
        return $collection;
    }

    /**
     * @param $item
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function getItemImage($item, $x = 54, $y = 54)
    {
        try {
            $imageUrl = $this->helper('Magento\Catalog\Helper\Image')->init($this->getProduct(), 'product_base_image')->constrainOnly(true)
                ->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->resize($x, $y)->getUrl();
        } catch (Exception $e) {
            $imageUrl = FALSE;
        }
        return $imageUrl;
    }

    /**
     * @return Image
     */
    public function getLookBookImg()
    {
        return $imageUrl = $this->_imageHelper->init($this->getProduct(), $this->_getLookBookAttribute());
    }

    /**
     * @return bool|string
     */
    public function getLookBookType()
    {
        switch ($this->getId()) {
            case 'lookbook_day':
                return Data::LOOKBOOK_TYPE_DAY;
            case 'lookbook_night':
                return Data::LOOKBOOK_TYPE_NIGHT;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCloseupIndexUrl()
    {
        return $this->getUrl('adminhtml/closeups/index/');
    }

    /**
     * @param $parentId
     * @param bool $required
     * @return mixed
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return $this->productLinks->getChildrenIds($parentId, Link::LINK_TYPE_GROUPED);
    }

    /**
     * @return mixed
     */
    public function getProductCollection()
    {
        $Ids = array(1, 2, 3);
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $Ids]);
        return $collection;
    }
}
