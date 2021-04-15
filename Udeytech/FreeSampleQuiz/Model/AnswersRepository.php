<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model;

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
use Udeytech\FreeSampleQuiz\Api\AnswersRepositoryInterface;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterface;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterfaceFactory;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersSearchResultsInterfaceFactory;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers as ResourceAnswers;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers\CollectionFactory as AnswersCollectionFactory;

/**
 * Class AnswersRepository
 * @package Udeytech\FreeSampleQuiz\Model
 */
class AnswersRepository implements AnswersRepositoryInterface
{

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var AnswersFactory
     */
    protected $answersFactory;
    /**
     * @var ResourceAnswers
     */
    protected $resource;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var AnswersCollectionFactory
     */
    protected $answersCollectionFactory;
    /**
     * @var AnswersInterfaceFactory
     */
    protected $dataAnswersFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var AnswersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;


    /**
     * @param ResourceAnswers $resource
     * @param AnswersFactory $answersFactory
     * @param AnswersInterfaceFactory $dataAnswersFactory
     * @param AnswersCollectionFactory $answersCollectionFactory
     * @param AnswersSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceAnswers $resource,
        AnswersFactory $answersFactory,
        AnswersInterfaceFactory $dataAnswersFactory,
        AnswersCollectionFactory $answersCollectionFactory,
        AnswersSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->resource = $resource;
        $this->answersFactory = $answersFactory;
        $this->answersCollectionFactory = $answersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataAnswersFactory = $dataAnswersFactory;
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
        AnswersInterface $answers
    )
    {
        /* if (empty($answers->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $answers->setStoreId($storeId);
        } */

        $answersData = $this->extensibleDataObjectConverter->toNestedArray(
            $answers,
            [],
            AnswersInterface::class
        );

        $answersModel = $this->answersFactory->create()->setData($answersData);

        try {
            $this->resource->save($answersModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the answers: %1',
                $exception->getMessage()
            ));
        }
        return $answersModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->answersCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            AnswersInterface::class
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
    public function deleteById($answersId)
    {
        return $this->delete($this->get($answersId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        AnswersInterface $answers
    )
    {
        try {
            $answersModel = $this->answersFactory->create();
            $this->resource->load($answersModel, $answers->getAnswersId());
            $this->resource->delete($answersModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Answers: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($answersId)
    {
        $answers = $this->answersFactory->create();
        $this->resource->load($answers, $answersId);
        if (!$answers->getId()) {
            throw new NoSuchEntityException(__('Answers with id "%1" does not exist.', $answersId));
        }
        return $answers->getDataModel();
    }
}

