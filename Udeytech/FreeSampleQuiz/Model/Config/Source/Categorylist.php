<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\FreeSampleQuiz\Model\Config\Source;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;
/**
 * Class Categorylist
 * @package Udeytech\FreeSampleQuiz\Model\Config\Source
 */
class Categorylist implements ArrayInterface {
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * Categorylist constructor.
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        CollectionFactory $categoryCollectionFactory
    ){
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }
    /**
     * @return array
     */
    public function toOptionArray(){
        $arr = $this->_toArray();
        $ret = [];
        foreach ($arr as $key => $value){
            $ret[] = ['value' => $key, 'label' => $value];
        }
        return $ret;
    }
    /**
     * @return array
     */
    private function _toArray(){
        $categories = $this->getCategoryCollection(false, false, false, false);
        $catagoryList = array();
        foreach ($categories as $category){
            $catagoryList[$category->getId()] = __($this->_getParentName($category->getPath()) . $category->getName());
        }
        return $catagoryList;
    }
    /**
     * @param bool $isActive
     * @param bool $level
     * @param bool $sortBy
     * @param bool $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryCollection($isActive = false, $level = false, $sortBy = false, $pageSize = false){
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
        if ($level) {
            $collection->addLevelFilter($level);
        }
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }
        return $collection;
    }
    /**
     * @param string $path
     * @return string
     */
    private function _getParentName($path = ''){
        $parentName = '';
        $rootCats = array(1, 2);
        $catTree = explode("/", $path);
        array_pop($catTree);
        if ($catTree && (count($catTree) > count($rootCats))) {
            foreach ($catTree as $catId) {
                if (!in_array($catId, $rootCats)) {
                    $category = $this->_categoryFactory->create()->load($catId);
                    $categoryName = $category->getName();
                    $parentName .= $categoryName . ' -> ';
                }
            }
        }
        return $parentName;
    }
}
