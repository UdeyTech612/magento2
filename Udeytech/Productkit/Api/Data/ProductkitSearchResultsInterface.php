<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProductkitSearchResultsInterface
 * @package Udeytech\Productkit\Api\Data
 */
interface ProductkitSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Productkit list.
     * @return ProductkitInterface[]
     */
    public function getItems();

    /**
     * Set choose_id list.
     * @param ProductkitInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

