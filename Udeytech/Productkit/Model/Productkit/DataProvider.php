<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Productkit\Model\Productkit;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Udeytech\Productkit\Model\ResourceModel\Productkit\Collection;
use Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory;

/**
 * Class DataProvider
 * @package Udeytech\Productkit\Model\Productkit
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var
     */
    protected $loadedData;
    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

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
            if ($temp['logo']):
                $img = [];
                $img[0]['image'] = $temp['logo'];
                $img[0]['url'] = $baseurl . 'logo/' . $temp['logo'];
                $temp['logo'] = $img;
            endif;
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('udeytech_productkit_productkit');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('udeytech_productkit_productkit');
        } else {
            if ($items):
                if ($model->getData('logo') != null) {
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

