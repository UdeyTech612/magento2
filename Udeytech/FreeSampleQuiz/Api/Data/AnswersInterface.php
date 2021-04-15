<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AnswersInterface
 * @package Udeytech\FreeSampleQuiz\Api\Data
 */
interface AnswersInterface extends ExtensibleDataInterface
{

    /**
     *
     */
    const ASSOCIATED_CODES = 'associated_codes';
    /**
     *
     */
    const ANSWER_ID = 'answer_id';
    /**
     *
     */
    const THUMB = 'thumb';
    /**
     *
     */
    const ANSWERS_ID = 'answers_id';
    /**
     *
     */
    const TITLE = 'title';

    /**
     * Get answers_id
     * @return string|null
     */
    public function getAnswersId();

    /**
     * Set answers_id
     * @param string $answersId
     * @return AnswersInterface
     */
    public function setAnswersId($answersId);

    /**
     * Get answer_id
     * @return string|null
     */
    public function getAnswerId();

    /**
     * Set answer_id
     * @param string $answerId
     * @return AnswersInterface
     */
    public function setAnswerId($answerId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return AnswersInterface
     */
    public function setTitle($title);

    /**
     * Get thumb
     * @return string|null
     */
    public function getThumb();

    /**
     * Set thumb
     * @param string $thumb
     * @return AnswersInterface
     */
    public function setThumb($thumb);

    /**
     * Get associated_codes
     * @return string|null
     */
    public function getAssociatedCodes();

    /**
     * Set associated_codes
     * @param string $associatedCodes
     * @return AnswersInterface
     */
    public function setAssociatedCodes($associatedCodes);
}

