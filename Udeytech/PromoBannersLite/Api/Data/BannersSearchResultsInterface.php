<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\PromoBannersLite\Api\Data;

/**
 * Interface BannersSearchResultsInterface
 * @package Udeytech\PromoBannersLite\Api\Data
 */
interface BannersSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get banners list.
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface[]
     */
    public function getItems();

    /**
     * Set utbanner_id list.
     * @param \Udeytech\PromoBannersLite\Api\Data\BannersInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

