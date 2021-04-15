<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Lookbook\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Udeytech\Lookbook\Api\Data\PositionsInterface;
use Udeytech\Lookbook\Api\Data\PositionsSearchResultsInterface;

/**
 * Interface PositionsRepositoryInterface
 * @package Udeytech\Lookbook\Api
 */
interface PositionsRepositoryInterface
{

    /**
     * Save Positions
     * @param PositionsInterface $positions
     * @return PositionsInterface
     * @throws LocalizedException
     */
    public function save(
        PositionsInterface $positions
    );

    /**
     * Retrieve Positions
     * @param string $positionsId
     * @return PositionsInterface
     * @throws LocalizedException
     */
    public function get($positionsId);

    /**
     * Retrieve Positions matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return PositionsSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Positions
     * @param PositionsInterface $positions
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        PositionsInterface $positions
    );

    /**
     * Delete Positions by ID
     * @param string $positionsId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($positionsId);
}

