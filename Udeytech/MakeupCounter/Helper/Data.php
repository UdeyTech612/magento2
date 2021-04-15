<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class Data
 * @package Udeytech\MakeupCounter\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const LOOKBOOK_DAY_ATTR = 'udeytech_lookbook_image';
    /**
     *
     */
    const LOOKBOOK_NIGHT_ATTR = 'udeytech_lookbook_image_night';
    /**
     *
     */
    const LOOKBOOK_CLOSEUPS_ATTR = 'udeytech_lookbook_close_ups';
    /**
     *
     */
    const LOOKBOOK_TYPE_DAY = 'day';
    /**
     *
     */
    const LOOKBOOK_TYPE_NIGHT = 'night';
    /**
     *
     */
    const MAKEUPCOUNTER_ATTRIBUTE_SET = 'Makeup Counter';
    /**
     *
     */
    const LOOK_TYPE_DAY_NIGHT = 1;
    /**
     *
     */
    const LOOK_TYPE_BEFORE_AFTER = 2;
    /**
     * @var
     */
    protected $_productCollection;
    /**
     * @var int
     */
    protected $_attributeSetId = 0;
    /**
     * @var
     */
    protected $productCollectionFactory;
    /**
     * @var
     */
    protected $categoriesCollection;
    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var Attribute\Set\CollectionFactory
     */
    protected $_attributeSetCollection;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        CollectionFactory $orderCollectionFactory,
        Attribute $eavAttribute,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->_attributeSetCollection = $attributeSetCollection;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Get lookbook products collection
     */
    public function getLookbookCollection()
    {
        if (is_null($this->_productCollection)) {
            $lookBookCollection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter(self::LOOKBOOK_DAY_ATTR, array('notnull' => true))
                ->addAttributeToFilter(self::LOOKBOOK_NIGHT_ATTR, array('notnull' => true))
                //->addAttributeToFilter(self::LOOKBOOK_CLOSEUPS_ATTR, array('notnull' => true))
                ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addAttributeToFilter('attribute_set_id', $this->getAttributeSetId())
                //->addAttributeToSort('created_at', Zend_Db_Select::SQL_ASC)
                ->applyFrontendPriceLimitations();
            $this->_productCollection = $lookBookCollection;
        }
        return $this->_productCollection;
    }
//    public function getAttributeSetId()
//    {
//        if(!$this->_attributeSetId) {
//            $model = Mage::getSingleton('catalog/config');
//            $entTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
//            $this->_attributeSetId = $model->getAttributeSetId($entTypeId, self::MAKEUPCOUNTER_ATTRIBUTE_SET);
//        }
//        return $this->_attributeSetId;
//    }
    /**
     * @return mixed
     */
    public function getAttributeSetId()
    {
        $attributeSet = $this->_attributeSetCollection->create()->addFieldToSelect('*')->addFieldToFilter('attribute_set_name', Self::MAKEUPCOUNTER_ATTRIBUTE_SET);
        foreach ($attributeSet as $attr):
            $_attributeSetId = $attr->getAttributeSetId();
        endforeach;
        return $_attributeSetId;
    }

    /**
     * @param $type
     * @return array
     */
    public function getTypeByValues($type)
    {
        switch ($type) {
            case self::LOOK_TYPE_DAY_NIGHT:
                $lookType = array(
                    "prev" => "day",
                    "next" => "night",
                );
                break;
            case self::LOOK_TYPE_BEFORE_AFTER:
                $lookType = array(
                    "prev" => "after",
                    "next" => "before",
                );
                break;
            default:
                $lookType = array(
                    "prev" => "prev",
                    "next" => "next",
                );
        }
        return $lookType;
    }
}
