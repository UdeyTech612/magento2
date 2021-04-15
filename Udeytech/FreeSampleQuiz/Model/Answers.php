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
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterface;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterfaceFactory;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers\Collection;

/**
 * Class Answers
 * @package Udeytech\FreeSampleQuiz\Model
 */
class Answers extends AbstractModel
{

    /**
     * @var AnswersInterfaceFactory
     */
    protected $answersDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'udeytech_freesamplequiz_answers';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AnswersInterfaceFactory $answersDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Answers $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AnswersInterfaceFactory $answersDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Answers $resource,
        Collection $resourceCollection,
        array $data = []
    )
    {
        $this->answersDataFactory = $answersDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve answers model with answers data
     * @return AnswersInterface
     */
    public function getDataModel()
    {
        $answersData = $this->getData();

        $answersDataObject = $this->answersDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $answersDataObject,
            $answersData,
            AnswersInterface::class
        );

        return $answersDataObject;
    }
}

