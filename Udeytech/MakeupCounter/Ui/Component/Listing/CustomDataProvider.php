<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

/**
 * Class CustomDataProvider
 */
class CustomDataProvider extends DataProvider
{
    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return [
            'items' => [
                [
                    'id' => 1,
                    'name' => 'First Item'
                ],
                [
                    'id' => 2,
                    'name' => 'Second Item'
                ],
                [
                    'id' => 3,
                    'name' => 'Third Item'
                ]
            ],
            'totalRecords' => 3
        ];
    }
}
