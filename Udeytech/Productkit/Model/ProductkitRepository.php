<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Model;

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
use Udeytech\Productkit\Api\Data\ProductkitInterface;
use Udeytech\Productkit\Api\Data\ProductkitInterfaceFactory;
use Udeytech\Productkit\Api\Data\ProductkitSearchResultsInterfaceFactory;
use Udeytech\Productkit\Api\ProductkitRepositoryInterface;
use Udeytech\Productkit\Model\ResourceModel\Productkit as ResourceProductkit;
use Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory as ProductkitCollectionFactory;

/**
 * Class ProductkitRepository
 * @package Udeytech\Productkit\Model
 */
class ProductkitRepository implements ProductkitRepositoryInterface
{

    /**
     * @var ResourceProductkit
     */
    protected $resource;
    /**
     * @var ProductkitFactory
     */
    protected $productkitFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var ProductkitInterfaceFactory
     */
    protected $dataProductkitFactory;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var ProductkitSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var ProductkitCollectionFactory
     */
    protected $productkitCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceProductkit $resource
     * @param ProductkitFactory $productkitFactory
     * @param ProductkitInterfaceFactory $dataProductkitFactory
     * @param ProductkitCollectionFactory $productkitCollectionFactory
     * @param ProductkitSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceProductkit $resource,
        ProductkitFactory $productkitFactory,
        ProductkitInterfaceFactory $dataProductkitFactory,
        ProductkitCollectionFactory $productkitCollectionFactory,
        ProductkitSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->resource = $resource;
        $this->productkitFactory = $productkitFactory;
        $this->productkitCollectionFactory = $productkitCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProductkitFactory = $dataProductkitFactory;
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
        ProductkitInterface $productkit
    )
    {
        /* if (empty($productkit->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $productkit->setStoreId($storeId);
        } */

        $productkitData = $this->extensibleDataObjectConverter->toNestedArray(
            $productkit,
            [],
            ProductkitInterface::class
        );
        $productkitModel = $this->productkitFactory->create()->setData($productkitData);
        try {
            $this->resource->save($productkitModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the productkit: %1',
                $exception->getMessage()
            ));
        }
        return $productkitModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->productkitCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            ProductkitInterface::class
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
    public function deleteById($productkitId)
    {
        return $this->delete($this->get($productkitId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        ProductkitInterface $productkit
    )
    {
        try {
            $productkitModel = $this->productkitFactory->create();
            $this->resource->load($productkitModel, $productkit->getProductkitId());
            $this->resource->delete($productkitModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Productkit: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productkitId)
    {
        $productkit = $this->productkitFactory->create();
        $this->resource->load($productkit, $productkitId);
        if (!$productkit->getId()) {
            throw new NoSuchEntityException(__('Productkit with id "%1" does not exist.', $productkitId));
        }
        return $productkit->getDataModel();
    }
}

