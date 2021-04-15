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
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterface;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsSearchResultsInterface;

/**
 * Interface QuestionsRepositoryInterface
 * @package Udeytech\FreeSampleQuiz\Api
 */
interface QuestionsRepositoryInterface
{

    /**
     * Save Questions
     * @param QuestionsInterface $questions
     * @return QuestionsInterface
     * @throws LocalizedException
     */
    public function save(
        QuestionsInterface $questions
    );

    /**
     * Retrieve Questions
     * @param string $questionsId
     * @return QuestionsInterface
     * @throws LocalizedException
     */
    public function get($questionsId);

    /**
     * Retrieve Questions matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return QuestionsSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Questions
     * @param QuestionsInterface $questions
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        QuestionsInterface $questions
    );

    /**
     * Delete Questions by ID
     * @param string $questionsId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($questionsId);
}
