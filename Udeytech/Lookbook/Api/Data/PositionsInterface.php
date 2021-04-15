<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Lookbook\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PositionsInterface
 * @package Udeytech\Lookbook\Api\Data
 */
interface PositionsInterface extends ExtensibleDataInterface
{

    /**
     *
     */
    const POSITIONS_ID = 'id';
    /**
     *
     */
    const POS_X = 'pos_x';
    /**
     *
     */
    const POS_Y = 'pos_y';
    /**
     *
     */
    const PARENT_ID = 'parent_id';
    /**
     *
     */
    const MAP = 'map';
    /**
     *
     */
    const SIMPLE_ID = 'simple_id';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $positionsId
     * @return PositionsInterface
     */
    public function setId($positionsId);

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param string $parentId
     * @return PositionsInterface
     */
    public function setParentId($parentId);

    /**
     * Get simple_id
     * @return string|null
     */
    public function getSimpleId();

    /**
     * Set simple_id
     * @param string $simpleId
     * @return PositionsInterface
     */
    public function setSimpleId($simpleId);

    /**
     * Get pos_x
     * @return string|null
     */
    public function getPosX();

    /**
     * Set pos_x
     * @param string $posX
     * @return PositionsInterface
     */
    public function setPosX($posX);

    /**
     * Get pos_y
     * @return string|null
     */
    public function getPosY();

    /**
     * Set pos_y
     * @param string $posY
     * @return PositionsInterface
     */
    public function setPosY($posY);

    /**
     * Get map
     * @return string|null
     */
    public function getMap();

    /**
     * Set map
     * @param string $map
     * @return PositionsInterface
     */
    public function setMap($map);
}

