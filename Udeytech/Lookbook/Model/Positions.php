<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Lookbook\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Udeytech\Lookbook\Api\Data\PositionsInterface;
use Udeytech\Lookbook\Api\Data\PositionsInterfaceFactory;
use Udeytech\Lookbook\Model\ResourceModel\Positions\Collection;

/**
 * Class Positions
 * @package Udeytech\Lookbook\Model
 */
class Positions extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'udeytech_lookbook_positions';
    /**
     * @var PositionsInterfaceFactory
     */
    protected $positionsDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param PositionsInterfaceFactory $positionsDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Positions $resource
     * @param Collection $resourceCollection
     * @param array $data
     */

    public function __construct(
        Context $context,
        Registry $registry,
        PositionsInterfaceFactory $positionsDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Positions $resource,
        Collection $resourceCollection,
        array $data = []
    )
    {
        $this->positionsDataFactory = $positionsDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve positions model with positions data
     * @return PositionsInterface
     */
    public function getDataModel()
    {
        $positionsData = $this->getData();
        $positionsDataObject = $this->positionsDataFactory->create();
        $this->dataObjectHelper->populateWithArray($positionsDataObject, $positionsData, PositionsInterface::class);
        return $positionsDataObject;
    }

    /**
     * @return float|int
     */
    public function getPosX()
    {
        return parent::getPosX() / 100000000;
    }

    /**
     * @return float|int
     */
    public function getPosY()
    {
        return parent::getPosY() / 100000000;
    }
}

