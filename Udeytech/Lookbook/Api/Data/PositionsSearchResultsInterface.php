<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Lookbook\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface PositionsSearchResultsInterface
 * @package Udeytech\Lookbook\Api\Data
 */
interface PositionsSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Positions list.
     * @return PositionsInterface[]
     */
    public function getItems();

    /**
     * Set parent_id list.
     * @param PositionsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

