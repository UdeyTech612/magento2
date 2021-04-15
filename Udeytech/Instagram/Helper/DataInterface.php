<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Helper;

/**
 * Interface DataInterface
 * @package Udeytech\Instagram\Helper
 */
interface DataInterface
{

    /**
     * Return Api key
     *
     * @return string|null
     */
    public function getClientId();

    /**
     * Return cache lifetime in seconds
     *
     * @return int|null
     */
    public function getCacheLifetime();

    /**
     * Return api callback function
     *
     * @return callable|null
     */
    public function getApiCallback();

    /**
     * @return mixed|null
     */
    public function getApiSecret();
}
