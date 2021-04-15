<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QuestionsSearchResultsInterface
 * @package Udeytech\FreeSampleQuiz\Api\Data
 */
interface QuestionsSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Questions list.
     * @return QuestionsInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param QuestionsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

