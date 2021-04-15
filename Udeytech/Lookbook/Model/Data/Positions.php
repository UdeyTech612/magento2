<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Lookbook\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Udeytech\Lookbook\Api\Data\PositionsExtensionInterface;
use Udeytech\Lookbook\Api\Data\PositionsInterface;

/**
 * Class Positions
 * @package Udeytech\Lookbook\Model\Data
 */
class Positions extends AbstractExtensibleObject implements PositionsInterface
{

    /**
     * Get id
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::POSITIONS_ID);
    }

    /**
     * Set
     * id
     * @param string $positionsId
     * @return PositionsInterface
     */
    public function setId($positionsId)
    {
        return $this->setData(self::POSITIONS_ID, $positionsId);
    }

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Set parent_id
     * @param string $parentId
     * @return PositionsInterface
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return PositionsExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param PositionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        PositionsExtensionInterface $extensionAttributes
    )
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get simple_id
     * @return string|null
     */
    public function getSimpleId()
    {
        return $this->_get(self::SIMPLE_ID);
    }

    /**
     * Set simple_id
     * @param string $simpleId
     * @return PositionsInterface
     */
    public function setSimpleId($simpleId)
    {
        return $this->setData(self::SIMPLE_ID, $simpleId);
    }

    /**
     * Get pos_x
     * @return string|null
     */
    public function getPosX()
    {
        return $this->_get(self::POS_X);
    }

    /**
     * Set pos_x
     * @param string $posX
     * @return PositionsInterface
     */
    public function setPosX($posX)
    {
        return $this->setData(self::POS_X, $posX);
    }

    /**
     * Get pos_y
     * @return string|null
     */
    public function getPosY()
    {
        return $this->_get(self::POS_Y);
    }

    /**
     * Set pos_y
     * @param string $posY
     * @return PositionsInterface
     */
    public function setPosY($posY)
    {
        return $this->setData(self::POS_Y, $posY);
    }

    /**
     * Get map
     * @return string|null
     */
    public function getMap()
    {
        return $this->_get(self::MAP);
    }

    /**
     * Set map
     * @param string $map
     * @return PositionsInterface
     */
    public function setMap($map)
    {
        return $this->setData(self::MAP, $map);
    }
}

