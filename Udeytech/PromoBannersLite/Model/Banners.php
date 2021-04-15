<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\PromoBannersLite\Model;

use Magento\Framework\Api\DataObjectHelper;
use Udeytech\PromoBannersLite\Api\Data\BannersInterface;
use Udeytech\PromoBannersLite\Api\Data\BannersInterfaceFactory;

/**
 * Class Banners
 * @package Udeytech\PromoBannersLite\Model
 */
class Banners extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var BannersInterfaceFactory
     */
    protected $bannersDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'udeytech_promobannerslite_banners';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param BannersInterfaceFactory $bannersDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Udeytech\PromoBannersLite\Model\ResourceModel\Banners $resource
     * @param \Udeytech\PromoBannersLite\Model\ResourceModel\Banners\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        BannersInterfaceFactory $bannersDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Udeytech\PromoBannersLite\Model\ResourceModel\Banners $resource,
        \Udeytech\PromoBannersLite\Model\ResourceModel\Banners\Collection $resourceCollection,
        array $data = []
    ) {
        $this->bannersDataFactory = $bannersDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve banners model with banners data
     * @return BannersInterface
     */
    public function getDataModel()
    {
        $bannersData = $this->getData();
        
        $bannersDataObject = $this->bannersDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $bannersDataObject,
            $bannersData,
            BannersInterface::class
        );
        
        return $bannersDataObject;
    }
}

