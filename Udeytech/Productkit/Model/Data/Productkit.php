<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Udeytech\Productkit\Api\Data\ProductkitExtensionInterface;
use Udeytech\Productkit\Api\Data\ProductkitInterface;

/**
 * Class Productkit
 * @package Udeytech\Productkit\Model\Data
 */
class Productkit extends AbstractExtensibleObject implements ProductkitInterface
{

    /**
     * Get productkit_id
     * @return string|null
     */
    public function getProductkitId()
    {
        return $this->_get(self::PRODUCTKIT_ID);
    }

    /**
     * Set productkit_id
     * @param string $productkitId
     * @return ProductkitInterface
     */
    public function setProductkitId($productkitId)
    {
        return $this->setData(self::PRODUCTKIT_ID, $productkitId);
    }

    /**
     * Get choose_id
     * @return string|null
     */
    public function getChooseId()
    {
        return $this->_get(self::CHOOSE_ID);
    }

    /**
     * Set choose_id
     * @param string $chooseId
     * @return ProductkitInterface
     */
    public function setChooseId($chooseId)
    {
        return $this->setData(self::CHOOSE_ID, $chooseId);
    }

    /**
     * Get kit_type
     * @return string|null
     */
    public function getKitType()
    {
        return $this->_get(self::KIT_TYPE);
    }

    /**
     * Set kit_type
     * @param string $kitType
     * @return ProductkitInterface
     */
    public function setKitType($kitType)
    {
        return $this->setData(self::KIT_TYPE, $kitType);
    }

    /**
     * Get kit_choose_title
     * @return string|null
     */
    public function getKitChooseTitle()
    {
        return $this->_get(self::KIT_CHOOSE_TITLE);
    }

    /**
     * Set kit_choose_title
     * @param string $kitChooseTitle
     * @return ProductkitInterface
     */
    public function setKitChooseTitle($kitChooseTitle)
    {
        return $this->setData(self::KIT_CHOOSE_TITLE, $kitChooseTitle);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return ProductkitInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get selected_products
     * @return string|null
     */
    public function getSelectedProducts()
    {
        return $this->_get(self::SELECTED_PRODUCTS);
    }

    /**
     * Set selected_products
     * @param string $selectedProducts
     * @return ProductkitInterface
     */
    public function setSelectedProducts($selectedProducts)
    {
        return $this->setData(self::SELECTED_PRODUCTS, $selectedProducts);
    }

    /**
     * Get selected_category
     * @return string|null
     */
    public function getSelectedCategory()
    {
        return $this->_get(self::SELECTED_CATEGORY);
    }

    /**
     * Set selected_category
     * @param string $selectedCategory
     * @return ProductkitInterface
     */
    public function setSelectedCategory($selectedCategory)
    {
        return $this->setData(self::SELECTED_CATEGORY, $selectedCategory);
    }

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->_get(self::THUMBNAIL);
    }

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return ProductkitInterface
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return ProductkitInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get kit_items_count
     * @return string|null
     */
    public function getKitItemsCount()
    {
        return $this->_get(self::KIT_ITEMS_COUNT);
    }

    /**
     * Set kit_items_count
     * @param string $kitItemsCount
     * @return ProductkitInterface
     */
    public function setKitItemsCount($kitItemsCount)
    {
        return $this->setData(self::KIT_ITEMS_COUNT, $kitItemsCount);
    }

    /**
     * Get status
     * @return string|null
     */

    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return ProductkitInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

