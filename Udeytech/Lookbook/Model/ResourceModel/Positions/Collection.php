<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Lookbook\Model\ResourceModel\Positions;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Udeytech\Lookbook\Model\ResourceModel\Positions;

/**
 * Class Collection
 * @package Udeytech\Lookbook\Model\ResourceModel\Positions
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Udeytech\Lookbook\Model\Positions::class,
            Positions::class
        );
    }
}

