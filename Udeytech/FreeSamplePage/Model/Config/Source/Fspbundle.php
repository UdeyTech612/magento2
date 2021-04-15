<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSamplePage\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Fspbundle
 * @package Udeytech\FreeSamplePage\Model\Config\Source
 */
class Fspbundle implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrieve all options array
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('type_id', 'bundle');
        $ret = [];
        foreach ($collection as $product) {
            $ret[] = ['value' => $product->getId(), 'label' => $product->getName(),];
        }
        return $ret;
    }
}
