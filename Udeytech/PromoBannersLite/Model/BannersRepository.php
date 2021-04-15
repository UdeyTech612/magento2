<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\PromoBannersLite\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Udeytech\PromoBannersLite\Api\BannersRepositoryInterface;
use Udeytech\PromoBannersLite\Api\Data\BannersInterfaceFactory;
use Udeytech\PromoBannersLite\Api\Data\BannersSearchResultsInterfaceFactory;
use Udeytech\PromoBannersLite\Model\ResourceModel\Banners as ResourceBanners;
use Udeytech\PromoBannersLite\Model\ResourceModel\Banners\CollectionFactory as BannersCollectionFactory;

/**
 * Class BannersRepository
 * @package Udeytech\PromoBannersLite\Model
 */
class BannersRepository implements BannersRepositoryInterface
{

    /**
     * @var BannersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var ResourceBanners
     */
    protected $resource;

    /**
     * @var BannersCollectionFactory
     */
    protected $bannersCollectionFactory;

    /**
     * @var BannersFactory
     */
    protected $bannersFactory;

    /**
     * @var BannersInterfaceFactory
     */
    protected $dataBannersFactory;


    /**
     * @param ResourceBanners $resource
     * @param BannersFactory $bannersFactory
     * @param BannersInterfaceFactory $dataBannersFactory
     * @param BannersCollectionFactory $bannersCollectionFactory
     * @param BannersSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceBanners $resource,
        BannersFactory $bannersFactory,
        BannersInterfaceFactory $dataBannersFactory,
        BannersCollectionFactory $bannersCollectionFactory,
        BannersSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->bannersFactory = $bannersFactory;
        $this->bannersCollectionFactory = $bannersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBannersFactory = $dataBannersFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
    ) {
        /* if (empty($banners->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $banners->setStoreId($storeId);
        } */
        
        $bannersData = $this->extensibleDataObjectConverter->toNestedArray(
            $banners,
            [],
            \Udeytech\PromoBannersLite\Api\Data\BannersInterface::class
        );
        
        $bannersModel = $this->bannersFactory->create()->setData($bannersData);
        
        try {
            $this->resource->save($bannersModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the banners: %1',
                $exception->getMessage()
            ));
        }
        return $bannersModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($bannersId)
    {
        $banners = $this->bannersFactory->create();
        $this->resource->load($banners, $bannersId);
        if (!$banners->getId()) {
            throw new NoSuchEntityException(__('banners with id "%1" does not exist.', $bannersId));
        }
        return $banners->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->bannersCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Udeytech\PromoBannersLite\Api\Data\BannersInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Udeytech\PromoBannersLite\Api\Data\BannersInterface $banners
    ) {
        try {
            $bannersModel = $this->bannersFactory->create();
            $this->resource->load($bannersModel, $banners->getBannersId());
            $this->resource->delete($bannersModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the banners: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($bannersId)
    {
        return $this->delete($this->get($bannersId));
    }
}

