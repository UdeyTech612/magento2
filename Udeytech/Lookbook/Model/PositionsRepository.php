<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Lookbook\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Udeytech\Lookbook\Api\Data\PositionsInterface;
use Udeytech\Lookbook\Api\Data\PositionsInterfaceFactory;
use Udeytech\Lookbook\Api\Data\PositionsSearchResultsInterfaceFactory;
use Udeytech\Lookbook\Api\PositionsRepositoryInterface;
use Udeytech\Lookbook\Model\ResourceModel\Positions as ResourcePositions;
use Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory as PositionsCollectionFactory;

/**
 * Class PositionsRepository
 * @package Udeytech\Lookbook\Model
 */
class PositionsRepository implements PositionsRepositoryInterface
{

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var PositionsInterfaceFactory
     */
    protected $dataPositionsFactory;
    /**
     * @var PositionsFactory
     */
    protected $positionsFactory;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ResourcePositions
     */
    protected $resource;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var PositionsCollectionFactory
     */
    protected $positionsCollectionFactory;
    /**
     * @var PositionsSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourcePositions $resource
     * @param PositionsFactory $positionsFactory
     * @param PositionsInterfaceFactory $dataPositionsFactory
     * @param PositionsCollectionFactory $positionsCollectionFactory
     * @param PositionsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePositions $resource,
        PositionsFactory $positionsFactory,
        PositionsInterfaceFactory $dataPositionsFactory,
        PositionsCollectionFactory $positionsCollectionFactory,
        PositionsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->resource = $resource;
        $this->positionsFactory = $positionsFactory;
        $this->positionsCollectionFactory = $positionsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPositionsFactory = $dataPositionsFactory;
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
        PositionsInterface $positions
    )
    {
        /* if (empty($positions->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $positions->setStoreId($storeId);
        } */

        $positionsData = $this->extensibleDataObjectConverter->toNestedArray(
            $positions,
            [],
            PositionsInterface::class
        );

        $positionsModel = $this->positionsFactory->create()->setData($positionsData);

        try {
            $this->resource->save($positionsModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the positions: %1',
                $exception->getMessage()
            ));
        }
        return $positionsModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->positionsCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            PositionsInterface::class
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
    public function deleteById($positionsId)
    {
        return $this->delete($this->get($positionsId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        PositionsInterface $positions
    )
    {
        try {
            $positionsModel = $this->positionsFactory->create();
            $this->resource->load($positionsModel, $positions->getPositionsId());
            $this->resource->delete($positionsModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Positions: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($positionsId)
    {
        $positions = $this->positionsFactory->create();
        $this->resource->load($positions, $positionsId);
        if (!$positions->getId()) {
            throw new NoSuchEntityException(__('Positions with id "%1" does not exist.', $positionsId));
        }
        return $positions->getDataModel();
    }
}

