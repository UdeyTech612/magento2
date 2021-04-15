<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Framework\Model\Context;
use Udeytech\Productkit\Model\ResourceModel\Expert\Collections;

/**
 * Class SalesOrderItem
 * @package Udeytech\Productkit\Model\Rewrite
 */

class SalesOrderItem extends \Magento\Sales\Model\Order\Item {
    /**
     * @var Collections
     */

    protected $_expertCollection;

    /**
     * SalesOrderItem constructor.
     * @param Context $context
     * @param Collections $expertCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        $this->_registry = $registry;
        parent::_construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getProductOptions() {
        $options = parent::getProductOptions();
        if (isset($options['options'])) {
            if (!$this->isItemOptionsHasPrice()) {
                foreach ($options['options'] as $key => $_option) {
                    $options['options'][$key]['value'] = '-';
                    $options['options'][$key]['print_value'] = '-';
                }
            }
        }
        return $options;
    }

    /**
     * @return bool
     */
    public function isItemOptionsHasPrice()
    {
        if ($this->getKitChoose())
            return !((bool)$this->getKitChoose()->getPrice());
        return true;
    }

    /**
     * @return bool|Collections
     */
    public function getKitChoose()
    {
        if (!$this->helper('\Udeytecch\Productkit\Helper\Data')->isKitProduct($this->getProduct()->getSku())) {
            return false;
        }
        $kitChoose = $this->_expertCollection->load($this->getProductkitChooseId());
        if (!$kitChoose->getId())
            $kitChoose = false;
        return $kitChoose;
    }
}
