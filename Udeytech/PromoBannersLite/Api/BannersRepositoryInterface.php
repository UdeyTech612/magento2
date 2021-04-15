<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\PromoBannersLite\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface BannersRepositoryInterface
 * @package Udeytech\PromoBannersLite\Api
 */
interface BannersRepositoryInterface
{

    /**
     * Save banners
     * @param \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
    );

    /**
     * Retrieve banners
     * @param string $bannersId
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($bannersId);

    /**
     * Retrieve banners matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Udeytech\PromoBannersLite\Api\Data\BannersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete banners
     * @param \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
    );

    /**
     * Delete banners by ID
     * @param string $bannersId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannersId);
}

