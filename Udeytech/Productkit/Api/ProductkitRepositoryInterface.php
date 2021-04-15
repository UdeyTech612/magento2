<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Udeytech\Productkit\Api\Data\ProductkitInterface;
use Udeytech\Productkit\Api\Data\ProductkitSearchResultsInterface;

/**
 * Interface ProductkitRepositoryInterface
 * @package Udeytech\Productkit\Api
 */
interface ProductkitRepositoryInterface
{

    /**
     * Save Productkit
     * @param ProductkitInterface $productkit
     * @return ProductkitInterface
     * @throws LocalizedException
     */
    public function save(
        ProductkitInterface $productkit
    );

    /**
     * Retrieve Productkit
     * @param string $productkitId
     * @return ProductkitInterface
     * @throws LocalizedException
     */
    public function get($productkitId);

    /**
     * Retrieve Productkit matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return ProductkitSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Productkit
     * @param ProductkitInterface $productkit
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        ProductkitInterface $productkit
    );

    /**
     * Delete Productkit by ID
     * @param string $productkitId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($productkitId);
}

