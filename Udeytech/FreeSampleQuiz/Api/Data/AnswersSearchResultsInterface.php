<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface AnswersSearchResultsInterface
 * @package Udeytech\FreeSampleQuiz\Api\Data
 */
interface AnswersSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Answers list.
     * @return AnswersInterface[]
     */
    public function getItems();

    /**
     * Set answer_id list.
     * @param AnswersInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

