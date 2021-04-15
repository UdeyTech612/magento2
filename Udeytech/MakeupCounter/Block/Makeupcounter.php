<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\MakeupCounter\Block;

use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Makeupcounter
 * @package Udeytech\MakeupCounter\Block
 */
class Makeupcounter extends Template
{
    /**
     * @var bool
     */
    protected $_getLookbookCollection = false;
    /**
     * @var bool
     */
    protected $_allLooks = false;
    /**
     * @var array
     */
    protected $_previousMonth = array();
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
     * @var DateTime
     */
    protected $date;
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        CollectionFactory $orderCollectionFactory,
        DateTime $date,
        Registry $registry,
        Data $helper,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoriesCollection = $categoriesCollection;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_helper = $helper;
        $this->date = $date;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get lookBook products collections
     * @return /Magento/Catalog/Model/Resource/Product/Collection
     */
    public function getAllLooks()
    {
        if (!$this->_allLooks) {
            $dateModel = $this->date;
            $previousMonth = $dateModel->gmtDate('m', '-1 month');
            $collection = $this->_getLookbookCollection();
            // $collection->addAttributeToSort('created_at', $dir = Varien_Data_Collection::SORT_ORDER_ASC);
            foreach ($collection as $item) {
                $item->setMonthName($this->_getMonthName($item->getCreatedAt()));
                $itemMonth = $dateModel->gmtDate('m', $item->getCreatedAt());
                if ($itemMonth === $previousMonth) {
                    $this->_previousMonth[] = $item;
                }
            }
            $this->_allLooks = $collection;
        }
        return $this->_allLooks;
    }

    /**
     * Get lookBook products collections
     * @return /Magento/Catalog/Model/Resource/Product/Collection
     */

    protected function _getLookbookCollection()
    {
        if (!$this->_getLookbookCollection) {
            $attributeSetId = $this->_helper->getAttributeSetId();
            $this->_getLookbookCollection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('attribute_set_id', $attributeSetId)
                ->applyFrontendPriceLimitations();
        }
        return $this->_getLookbookCollection;
    }

    /**
     * Get month name by date
     * @param $date
     * @return string
     */
    protected function _getMonthName($date)
    {
        $result = $this->date->date('F Y', $date);
        return $result;
    }

    /**
     * Get lookBook products collections for the previous month
     *
     * @return array
     */
    public function getPreviousMonthLooks()
    {
        return $this->_previousMonth;
    }

    /**
     * Render CMS block set in Make Up Tips CMS Block
     * @return string
     */
    public function getTipBlockContent()
    {
        // $content = '';
        // $look = $this->_registry->registry('current_product');
        //if (!is_null($look) && $look->getMakeupTips() > 0) {
        $content = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId('makeup-tips-makeup-diary-austin')->toHtml();
        //}
        return $content;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }
}

