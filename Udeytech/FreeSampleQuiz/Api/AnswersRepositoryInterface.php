<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterface;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersSearchResultsInterface;

/**
 * Interface AnswersRepositoryInterface
 * @package Udeytech\FreeSampleQuiz\Api
 */
interface AnswersRepositoryInterface
{

    /**
     * Save Answers
     * @param AnswersInterface $answers
     * @return AnswersInterface
     * @throws LocalizedException
     */
    public function save(
        AnswersInterface $answers
    );

    /**
     * Retrieve Answers
     * @param string $answersId
     * @return AnswersInterface
     * @throws LocalizedException
     */
    public function get($answersId);

    /**
     * Retrieve Answers matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return AnswersSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Answers
     * @param AnswersInterface $answers
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        AnswersInterface $answers
    );

    /**
     * Delete Answers by ID
     * @param string $answersId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($answersId);
}

