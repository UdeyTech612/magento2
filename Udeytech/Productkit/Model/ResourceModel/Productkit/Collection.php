<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Model\ResourceModel\Productkit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Udeytech\Productkit\Model\ResourceModel\Productkit;

/**
 * Class Collection
 * @package Udeytech\Productkit\Model\ResourceModel\Productkit
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'productkit_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Udeytech\Productkit\Model\Productkit::class,
            Productkit::class
        );
    }
}

