<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterface;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterfaceFactory;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Questions\Collection;

/**
 * Class Questions
 * @package Udeytech\FreeSampleQuiz\Model
 */
class Questions extends AbstractModel
{

    /**
     * @var QuestionsInterfaceFactory
     */
    protected $questionsDataFactory;

    /**
     * @var string
     */
    protected $_eventPrefix = 'udeytech_freesamplequiz_questions';
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param QuestionsInterfaceFactory $questionsDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Questions $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        QuestionsInterfaceFactory $questionsDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Questions $resource,
        Collection $resourceCollection,
        array $data = []
    )
    {
        $this->questionsDataFactory = $questionsDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve questions model with questions data
     * @return QuestionsInterface
     */
    public function getDataModel()
    {
        $questionsData = $this->getData();

        $questionsDataObject = $this->questionsDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $questionsDataObject,
            $questionsData,
            QuestionsInterface::class
        );

        return $questionsDataObject;
    }
}

