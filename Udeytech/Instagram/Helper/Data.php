<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Udeytech\Instagram\Helper
 */
class Data extends AbstractHelper implements DataInterface
{
    /**
     *
     */
    const INSTAGRAM_MODE_BY_USER_ID = 1;
    /**
     *
     */
    const INSTAGRAM_MODE_BY_HASHTAG = 2;
    /**
     *
     */
    const INSTAGRAM_MODE_BY_PRODUCT_HASHTAG = 3;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getClientId()
    {
        return (string)$this->scopeConfig->getValue(
            'udeytech_instagram/general/api_client_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return (int)$this->scopeConfig->getValue(
            'udeytech_instagram/general/cache_lifetime',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return null
     */
    public function getApiCallback()
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @return null
     */
    public function getApiSecret()
    {
        return null;
    }

}
