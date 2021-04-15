<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Mode
 * @package Udeytech\Instagram\Model\Source
 */
class Mode implements ArrayInterface
{
    /**
     *
     */
    const BY_USER_ID_CODE = 1;
    /**
     *
     */
    const BY_USER_ID_LABEL = 'By User ID';

    /**
     *
     */
    const BY_HASHTAG_CODE = 2;
    /**
     *
     */
    const BY_HASHTAG_LABEL = 'By Hashtag';

    /**
     *
     */
    const BY_PRODUCT_HASHTAG_CODE = 3;
    /**
     *
     */
    const BY_PRODUCT_HASHTAG_LABEL = 'By Product Hashtag';

    /**
     *
     */
    const BY_USER_NAME_CODE = 4;
    /**
     *
     */
    const BY_USER_NAME_LABEL = 'By User Name';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::BY_USER_NAME_CODE,
                'label' => __(self::BY_USER_NAME_LABEL)
            ],
            [
                'value' => self::BY_USER_ID_CODE,
                'label' => __(self::BY_USER_ID_LABEL)
            ],
            [
                'value' => self::BY_PRODUCT_HASHTAG_CODE,
                'label' => __(self::BY_PRODUCT_HASHTAG_LABEL)
            ],
            [
                'value' => self::BY_HASHTAG_CODE,
                'label' => __(self::BY_HASHTAG_LABEL)
            ],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::BY_USER_NAME_CODE => __(self::BY_USER_NAME_LABEL),
            self::BY_USER_ID_CODE => __(self::BY_USER_ID_LABEL),
            self::BY_PRODUCT_HASHTAG_CODE => __(self::BY_PRODUCT_HASHTAG_LABEL),
            self::BY_HASHTAG_CODE => __(self::BY_HASHTAG_LABEL),
        ];
    }
}
