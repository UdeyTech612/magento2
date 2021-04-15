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
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterface;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterfaceFactory;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsSearchResultsInterfaceFactory;
use Udeytech\FreeSampleQuiz\Api\QuestionsRepositoryInterface;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions as ResourceQuestions;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions\CollectionFactory as QuestionsCollectionFactory;

/**
 * Class QuestionsRepository
 * @package Udeytech\FreeSampleQuiz\Model
 */
class QuestionsRepository implements QuestionsRepositoryInterface
{

    /**
     * @var QuestionsCollectionFactory
     */
    protected $questionsCollectionFactory;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var ResourceQuestions
     */
    protected $resource;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var QuestionsInterfaceFactory
     */
    protected $dataQuestionsFactory;
    /**
     * @var QuestionsFactory
     */
    protected $questionsFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var QuestionsSearchResultsInterfaceFactory
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
     * @param ResourceQuestions $resource
     * @param QuestionsFactory $questionsFactory
     * @param QuestionsInterfaceFactory $dataQuestionsFactory
     * @param QuestionsCollectionFactory $questionsCollectionFactory
     * @param QuestionsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceQuestions $resource,
        QuestionsFactory $questionsFactory,
        QuestionsInterfaceFactory $dataQuestionsFactory,
        QuestionsCollectionFactory $questionsCollectionFactory,
        QuestionsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    )
    {
        $this->resource = $resource;
        $this->questionsFactory = $questionsFactory;
        $this->questionsCollectionFactory = $questionsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuestionsFactory = $dataQuestionsFactory;
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
        QuestionsInterface $questions
    )
    {
        /* if (empty($questions->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $questions->setStoreId($storeId);
        } */

        $questionsData = $this->extensibleDataObjectConverter->toNestedArray(
            $questions,
            [],
            QuestionsInterface::class
        );

        $questionsModel = $this->questionsFactory->create()->setData($questionsData);

        try {
            $this->resource->save($questionsModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the questions: %1',
                $exception->getMessage()
            ));
        }
        return $questionsModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->questionsCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            QuestionsInterface::class
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
    public function deleteById($questionsId)
    {
        return $this->delete($this->get($questionsId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        QuestionsInterface $questions
    )
    {
        try {
            $questionsModel = $this->questionsFactory->create();
            $this->resource->load($questionsModel, $questions->getQuestionsId());
            $this->resource->delete($questionsModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Questions: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($questionsId)
    {
        $questions = $this->questionsFactory->create();
        $this->resource->load($questions, $questionsId);
        if (!$questions->getId()) {
            throw new NoSuchEntityException(__('Questions with id "%1" does not exist.', $questionsId));
        }
        return $questions->getDataModel();
    }
}

