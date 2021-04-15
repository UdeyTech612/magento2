<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions;

/**
 * Class Collection
 * @package Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'questions_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Udeytech\FreeSampleQuiz\Model\Questions::class,
            Questions::class
        );
    }
}

