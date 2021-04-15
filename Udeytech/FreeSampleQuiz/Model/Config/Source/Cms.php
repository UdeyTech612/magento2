<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSampleQuiz\Model\Config\Source;

use Magento\Cms\Model\Page;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Cms
 * @package Udeytech\FreeSampleQuiz\Model\Config\Source
 */
class Cms implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        /**
         * @return array
         */
        $res = array();
        $objectManager = ObjectManager::getInstance();
        $collection = $objectManager->get('\Magento\Cms\Model\ResourceModel\Page\CollectionFactory')->create();
        $collection->addFieldToFilter('is_active', Page::STATUS_ENABLED);
        foreach ($collection as $page) {
            $data['value'] = $page->getData('identifier');
            $data['label'] = $page->getData('title');
            $res[] = $data;
        }
        return $res;
    }
}
