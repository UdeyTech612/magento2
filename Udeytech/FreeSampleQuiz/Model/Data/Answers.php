<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersExtensionInterface;
use Udeytech\FreeSampleQuiz\Api\Data\AnswersInterface;

/**
 * Class Answers
 * @package Udeytech\FreeSampleQuiz\Model\Data
 */
class Answers extends AbstractExtensibleObject implements AnswersInterface
{

    /**
     * Get answers_id
     * @return string|null
     */
    public function getAnswersId()
    {
        return $this->_get(self::ANSWERS_ID);
    }

    /**
     * Set answers_id
     * @param string $answersId
     * @return AnswersInterface
     */
    public function setAnswersId($answersId)
    {
        return $this->setData(self::ANSWERS_ID, $answersId);
    }

    /**
     * Get answer_id
     * @return string|null
     */
    public function getAnswerId()
    {
        return $this->_get(self::ANSWER_ID);
    }

    /**
     * Set answer_id
     * @param string $answerId
     * @return AnswersInterface
     */
    public function setAnswerId($answerId)
    {
        return $this->setData(self::ANSWER_ID, $answerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return AnswersExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param AnswersExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        AnswersExtensionInterface $extensionAttributes
    )
    {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return AnswersInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get thumb
     * @return string|null
     */
    public function getThumb()
    {
        return $this->_get(self::THUMB);
    }

    /**
     * Set thumb
     * @param string $thumb
     * @return AnswersInterface
     */
    public function setThumb($thumb)
    {
        return $this->setData(self::THUMB, $thumb);
    }

    /**
     * Get associated_codes
     * @return string|null
     */
    public function getAssociatedCodes()
    {
        return $this->_get(self::ASSOCIATED_CODES);
    }

    /**
     * Set associated_codes
     * @param string $associatedCodes
     * @return AnswersInterface
     */
    public function setAssociatedCodes($associatedCodes)
    {
        return $this->setData(self::ASSOCIATED_CODES, $associatedCodes);
    }
}

