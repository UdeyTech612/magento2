<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSampleQuiz\Model\Config\Source;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Questionlist
 * @package Udeytech\FreeSampleQuiz\Model\Config\Source
 */
class Questionlist implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $data) {
            $ret[] = [
                'value' => $data->getQuestionsId(),
                'label' => $data->getTitle()
            ];
        }
        return $ret;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $objectManager = ObjectManager::getInstance();
        $collection = $objectManager->create('Udeytech\FreeSampleQuiz\Model\Questions')->getCollection();
        return $collection;
    }
}

?>
