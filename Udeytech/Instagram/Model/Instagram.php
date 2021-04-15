<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Model;

use Exception;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Registry;
use Udeytech\Instagram\Helper\Config;
use Udeytech\Instagram\Model\Instagram\Api;

/**
 * Class Instagram
 * @package Udeytech\Instagram\Model
 */
class Instagram extends AbstractModel
{
    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Api
     */
    protected $_api;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var EntityFactory
     */
    protected $_entityFactory;

    /**
     * @param EntityFactory $entityFactory
     * @param ObjectManager $objectManager
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Config $configHelper,
        EntityFactory $entityFactory,
        ObjectManager $objectManager,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_configHelper = $configHelper;
        $this->_objectManager = $objectManager;
        $this->_entityFactory = $entityFactory;
        $this->_api = new Api(
            $this->_configHelper->getAccessToken()
        );

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param string $name
     * @param int $limit
     *
     * @return array|Collection
     */
    public function getTagMedia($name, $limit = 0)
    {
        try {
            $response = $this->_getApi()->getTagMedia($name, $limit);
        } catch (Exception $e) {
            $this->_logger->error($e);
            return [];
        }
        return $this->_processData($response);
    }

    /**
     * Returns Instagram API model.
     *
     * @return Api
     */
    protected function _getApi()
    {
        return $this->_api;
    }

    /**
     * @param $response
     * @return Collection
     * @throws Exception
     */
    protected function _processData($response)
    {
        $collection = new Collection(
            $this->_entityFactory
        );

        if (!isset($response->data) || !is_array($response->data)) {
            return $collection;
        }

        foreach ($response->data as $item) {
            if (!isset($item->images->low_resolution->url)) {
                continue;
            }

            /* @var DataObject $image */
            $image = $this->_objectManager->create('Magento\Framework\DataObject');

            $image->setUrl($item->images->low_resolution->url);

            if (isset($item->caption->text)) {
                $image->setName($item->caption->text);
            }

            if (isset($item->link)) {
                $image->setLink($item->link);
            }

            $collection->addItem($image);
        }
        return $collection;
    }

    /**
     * @param string $userName
     * @param int $limit
     *
     * @return array|Collection
     */
    public function getUserMediaByName($userName, $limit = 0)
    {
        try {
            $response = $this->_getApi()->searchUser($userName);
        } catch (Exception $e) {
            $this->_logger->error($e);
            return [];
        }
        if (!isset($response->data) || !is_array($response->data) || !count($response->data)) {
            return [];
        }
        $user = current($response->data);
        return $this->getUserMediaById($user->id, $limit);
    }

    /**
     * @param string $userId
     * @param int $limit
     *
     * @return array|Collection
     */
    public function getUserMediaById($userId, $limit = 0)
    {
        try {
            $response = $this->_getApi()->getUserMedia($userId, $limit);
        } catch (Exception $e) {
            $this->_logger->error($e);
            return [];
        }
        return $this->_processData($response);
    }
}
