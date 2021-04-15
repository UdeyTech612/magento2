<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductkitInterface
 * @package Udeytech\Productkit\Api\Data
 */
interface ProductkitInterface extends ExtensibleDataInterface
{

    /**
     *
     */
    const THUMBNAIL = 'thumbnail';
    /**
     *
     */
    const SELECTED_CATEGORY = 'selected_category';
    /**
     *
     */
    const PRICE = 'price';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const KIT_TYPE = 'kit_type';
    /**
     *
     */
    const KIT_ITEMS_COUNT = 'kit_items_count';
    /**
     *
     */
    const KIT_CHOOSE_TITLE = 'kit_choose_title';
    /**
     *
     */
    const CHOOSE_ID = 'choose_id';
    /**
     *
     */
    const SELECTED_PRODUCTS = 'selected_products';
    /**
     *
     */
    const DESCRIPTION = 'description';
    /**
     *
     */
    const PRODUCTKIT_ID = 'productkit_id';

    /**
     * Get productkit_id
     * @return string|null
     */
    public function getProductkitId();

    /**
     * Set productkit_id
     * @param string $productkitId
     * @return ProductkitInterface
     */
    public function setProductkitId($productkitId);

    /**
     * Get choose_id
     * @return string|null
     */
    public function getChooseId();

    /**
     * Set choose_id
     * @param string $chooseId
     * @return ProductkitInterface
     */
    public function setChooseId($chooseId);

    /**
     * Get kit_type
     * @return string|null
     */
    public function getKitType();

    /**
     * Set kit_type
     * @param string $kitType
     * @return ProductkitInterface
     */
    public function setKitType($kitType);

    /**
     * Get kit_choose_title
     * @return string|null
     */
    public function getKitChooseTitle();

    /**
     * Set kit_choose_title
     * @param string $kitChooseTitle
     * @return ProductkitInterface
     */
    public function setKitChooseTitle($kitChooseTitle);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return ProductkitInterface
     */
    public function setDescription($description);

    /**
     * Get selected_products
     * @return string|null
     */
    public function getSelectedProducts();

    /**
     * Set selected_products
     * @param string $selectedProducts
     * @return ProductkitInterface
     */
    public function setSelectedProducts($selectedProducts);

    /**
     * Get selected_category
     * @return string|null
     */
    public function getSelectedCategory();

    /**
     * Set selected_category
     * @param string $selectedCategory
     * @return ProductkitInterface
     */
    public function setSelectedCategory($selectedCategory);

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return ProductkitInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return ProductkitInterface
     */
    public function setPrice($price);

    /**
     * Get kit_items_count
     * @return string|null
     */
    public function getKitItemsCount();

    /**
     * Set kit_items_count
     * @param string $kitItemsCount
     * @return ProductkitInterface
     */
    public function setKitItemsCount($kitItemsCount);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return ProductkitInterface
     */
    public function setStatus($status);
}

