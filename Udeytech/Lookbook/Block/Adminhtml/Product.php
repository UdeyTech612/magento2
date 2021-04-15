<?php
/**
 * Copyright © udeytech.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Lookbook\Block\Adminhtml;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory;
use Udeytech\MakeupCounter\Helper\Data;

class Product extends Template
{
    protected $_collectionFactory;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * Constructor
     * @param Context  $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    public function getTabLabel()
    {
        switch ($this->getId()) {
            case 'lookbook_day': return __('LookBook Day');
            case 'lookbook_night': return __('LookBook Night');
        }
        return __('LookBook');
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
            $imageUrl = $this->helper('Magento\Catalog\Helper\Image')->init($product, $this->_getLookBookAttribute())
                ->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->getUrl();
        } catch (Exception $e) {
            $imageUrl = FALSE;
        }

        return ($imageUrl)?TRUE:FALSE;
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
        $collection = $this->_collectionFactory->create()->getCollection()
            ->addFieldToFilter('parent_id', $this->getProduct()->getEntityId())
            ->addFieldToFilter('simple_id', $itemId)
            ->addFieldToFilter('type', $this->getLookBookType())->getLastItem();
        return $collection;
    }
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
    public function getLookBookImg()
    {
        return $imageUrl = $this->helper('Magento\Catalog\Helper\Image')->init($this->getProduct(), $this->_getLookBookAttribute());
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
        return $this->getUrl('adminhtml/closeups/index/');
    }
    public function getCustomFunTest(){
        echo "testingNew";
    }
}

