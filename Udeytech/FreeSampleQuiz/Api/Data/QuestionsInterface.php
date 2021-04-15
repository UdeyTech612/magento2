<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QuestionsInterface
 * @package Udeytech\FreeSampleQuiz\Api\Data
 */
interface QuestionsInterface extends ExtensibleDataInterface
{
    /**
     *
     */
    const QUESTION_ID = 'question_id';
    /**
     *
     */
    const DESCRIPTION = 'description';
    /**
     *
     */
    const QUESTIONS_ID = 'questions_id';
    /**
     *
     */
    const TITLE = 'title';

    /**
     * Get questions_id
     * @return string|null
     */
    public function getQuestionsId();

    /**
     * Set questions_id
     * @param string $questionsId
     * @return QuestionsInterface
     */
    public function setQuestionsId($questionsId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return QuestionsInterface
     */
    public function setTitle($title);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return QuestionsInterface
     */
    public function setDescription($description);

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId();

    /**
     * Set question_id
     * @param string $questionId
     * @return QuestionsInterface
     */
    public function setQuestionId($questionId);
}

