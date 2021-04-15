<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsExtensionInterface;
use Udeytech\FreeSampleQuiz\Api\Data\QuestionsInterface;

/**
 * Class Questions
 * @package Udeytech\FreeSampleQuiz\Model\Data
 */
class Questions extends AbstractExtensibleObject implements QuestionsInterface
{

    /**
     * Get questions_id
     * @return string|null
     */
    public function getQuestionsId()
    {
        return $this->_get(self::QUESTIONS_ID);
    }

    /**
     * Set questions_id
     * @param string $questionsId
     * @return QuestionsInterface
     */
    public function setQuestionsId($questionsId)
    {
        return $this->setData(self::QUESTIONS_ID, $questionsId);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return QuestionsInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return QuestionsExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param QuestionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        QuestionsExtensionInterface $extensionAttributes
    )
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return QuestionsInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get question_id
     * @return string|null
     */
    public function getQuestionId()
    {
        return $this->_get(self::QUESTION_ID);
    }

    /**
     * Set question_id
     * @param string $questionId
     * @return QuestionsInterface
     */
    public function setQuestionId($questionId)
    {
        return $this->setData(self::QUESTION_ID, $questionId);
    }
}

