<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers;

/**
 * Class Collection
 * @package Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'answers_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Udeytech\FreeSampleQuiz\Model\Answers::class,
            Answers::class
        );
    }
//    protected function _initSelect()
//    {
//        parent::_initSelect();
//        $this->addFieldToFilter('main_table.questions_id', ['eq' => '3']);
//        return $this;
//    }
}

