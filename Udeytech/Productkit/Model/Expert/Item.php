<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Productkit\Model\Expert;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Item
 * @package Udeytech\Productkit\Model\Export
 */
class Item extends DataObject
{
    /**
     * @var Json
     */
    protected $_jsonSerializer;
    /**
     * @var string
     */
    private $_kitModel = '';
    /**
     * @var array
     */
    private $_items = array();

    /**
     * Item constructor.
     * @param Json $jsonSerializer
     */
    public function __construct(
        Json $jsonSerializer)
    {
        $this->_jsonSerializer = $jsonSerializer;
    }

    /**
     * @param $kit
     * @return $this
     */
    public function loadModelByKit($kit)
    {
        $this->_initKit($kit);
        return $this;
    }

    /**
     * @param $kit
     * @return $this
     */
    private function _initKit($kit)
    {
        $this->_kitModel = $kit;
        $this->_items = $this->_getItems();
        return $this;
    }

    /**
     * @return array|null
     */
    private function _getItems()
    {
        $selectedCategory = $this->_kitModel->getselectedCategory();
        $selectedCategoryArray = $this->_jsonSerializer->jsonDecode($selectedCategory);
        $items = array();
        if (!isset($selectedCategoryArray)) {
            return null;
        }
        foreach ($selectedCategoryArray as $id => $categories) {
            $categories = preg_replace('/[^0-9,]/', '', $categories);
            $categories = preg_replace('/,+/', ',', $categories);
            $categories = trim($categories, "^,");
            $categoriesArray = explode(',', $categories);
            $categoriesArray = array_unique($categoriesArray);
            sort($categoriesArray);
            $items[$id] = array(
                'id' => $id,
                'categories' => $categoriesArray
            );
        }
        return $items;
    }
    public function save(){
        if ($this->getId()) {
            $this->_items[$this->getId()] = $this->_toString($this->getCategories());
        } else {
            end($this->_items);
            $id = key($this->_items);
            $id++;
            $this->_items[$id] = $this->_toString($this->getCategories());
            $this->setId(key($this->_items));
        }
        $selectedCategory = $this->_jsonSerializer->jsonEncode(array_filter($this->_items));
        $this->_kitModel->setData('selected_category', $selectedCategory);
        $this->_kitModel->save();
        return $this;
    }
    protected function _toString($data)
    {
        if (is_array($data)) {
            $data = implode(",", $data);
        }
        $data = trim($data, "^,");
        return $data;
    }
    public function saveItems($items)
    {
        foreach ($items as $id => $item) {
            $this->setId($id)->setCategories($item)->save();
        }
    }
    public function getAllItems()
    {
        return $this->_items;
    }

    public function getAllCategories()
    {
        if (!isset($this->_items)) {
            return null;
        }
        $categories = array();
        foreach ($this->_items as $item) {
            $categories = array_merge($categories, $item['categories']);
            $categories = array_unique($categories);
            sort($categories);
        }
        return $categories;
    }

    /**
     * @return bool|Item
     */
    public function getFirstItem()
    {
        reset($this->_items);
        $key = key($this->_items);
        return $this->load($key);
    }

    /**
     * @param bool $itemId
     * @return $this|bool
     */
    public function load($itemId = false)
    {
        if (!$this->_kitModel) {
            return false;
        }
        if ($itemId && (int)$itemId > 0) {
            $this->setId($itemId);
            $this->setCategories($this->_items[$itemId]['categories']);
        }
        return $this;
    }

    /**
     * @return array|string
     */
    public function getCategoriesList()
    {
        $list = trim($this->getCategories(), "^,");
        $list = explode(',', $list);
        return $list;
    }
}

?>
