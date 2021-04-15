<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Udeytech\Productkit\Api\Data\ProductkitInterface;
use Udeytech\Productkit\Api\Data\ProductkitInterfaceFactory;
use Udeytech\Productkit\Model\ResourceModel\Productkit\Collection;

class Productkit extends AbstractModel
{

    protected $_eventPrefix = 'udeytech_productkit_productkit';
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    protected $productkitDataFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductkitInterfaceFactory $productkitDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Productkit $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductkitInterfaceFactory $productkitDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Productkit $resource,
        Collection $resourceCollection,
        array $data = []
    ){
        $this->productkitDataFactory = $productkitDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Retrieve productkit model with productkit data
     * @return ProductkitInterface
     */
    public function getDataModel(){
        $productkitData = $this->getData();
        $productkitDataObject = $this->productkitDataFactory->create();
        $this->dataObjectHelper->populateWithArray($productkitDataObject,$productkitData,ProductkitInterface::class);
        return $productkitDataObject;
    }
}

