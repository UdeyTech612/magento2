<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Model\Answers;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Udeytech\FreeSampleQuiz\Model\ResourceModel\Answers\CollectionFactory;

/**
 * Class DataProvider
 * @package Udeytech\FreeSampleQuiz\Model\Answers
 */
class DataProvider extends AbstractDataProvider
{

    /**
     * @var StoreManagerInterface
     */
    public $_storeManager;
    /**
     * @var
     */
    protected $collection;
    /**
     * @var
     */
    protected $loadedData;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $baseurl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $temp = $model->getData();
            if ($temp['thumb']):
                $img = [];
                $img[0]['image'] = $temp['thumb'];
                $img[0]['url'] = $baseurl . 'quiz/' . $temp['thumb'];
                $temp['thumb'] = $img;
            endif;
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('udeytech_freesamplequiz_answers');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('udeytech_freesamplequiz_answers');
        } else {
            if ($items):
                if ($model->getData('thumb') != null) {
                    $t2[$model->getId()] = $temp;
                    return $t2;
                } else {
                    return $this->loadedData;
                }
            endif;
        }
        return $this->loadedData;
    }
}

