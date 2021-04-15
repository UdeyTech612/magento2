<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Helper;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Udeytech\Instagram\Helper
 */
class Config extends AbstractHelper
{
    /**
     *
     */
    const GENERAL_CACHE_LIFETIME = 'udeytech_instagram/general/cache_lifetime';
    /**
     *
     */
    const API_ACCESS_TOKEN = 'udeytech_instagram/api/access_token';
    /**
     *
     */
    const API_CLIENT_ID = 'udeytech_instagram/api/client_id';
    /**
     *
     */
    const API_CLIENT_SECRET = 'udeytech_instagram/api/client_secret';
    /**
     *
     * @var EncryptorInterface
     */
    protected $_secHelper;
    /**
     *
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_configModel;
    /**
     *
     * @var ReinitableConfigInterface
     */
    protected $_appConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Config constructor.
     * @param Context $context
     * @param EncryptorInterface $encInterface
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param StoreManagerInterface $storeManager
     * @param ReinitableConfigInterface $config
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encInterface,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        StoreManagerInterface $storeManager,
        ReinitableConfigInterface $config
    )
    {
        parent::__construct($context);
        $this->_secHelper = $encInterface;
        $this->_configModel = $resourceConfig;
        $this->_appConfig = $config;
        $this->_storeManager = $storeManager;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {

        return (int)$this->scopeConfig->getValue(
            self::GENERAL_CACHE_LIFETIME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $accessToken
     */
    public function connect($accessToken)
    {
        $encryptedAccessToken = $this->_secHelper->encrypt($accessToken);
        $this->_saveConfig(self::API_ACCESS_TOKEN, $encryptedAccessToken);
    }

    /**
     * @param $path
     * @param $value
     * @param string $scope
     * @param int $scopeId
     */
    protected function _saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $this->_configModel->saveConfig($path, $value, $scope, $scopeId);
    }

    /**
     *
     */
    public function disconnect()
    {
        $this->_saveConfig(self::API_ACCESS_TOKEN, '');
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isConnected($store = null)
    {
        $token = $this->getConfigValue(self::API_ACCESS_TOKEN, $store);
        return !empty($token) ? true : false;
    }

    /**
     * @param $path
     * @param null $store
     * @return mixed
     */
    protected function getConfigValue($path, $store = null)
    {
        $store = $store ?
            $store->getId() : ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getAccessToken($store = null)
    {
        return $this->_secHelper->decrypt(
            $this->getConfigValue(self::API_ACCESS_TOKEN, $store));
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getClientId($store = null)
    {
        return $this->getConfigValue(self::API_CLIENT_ID, $store);
    }

    /**
     * @param $clientId
     */
    public function saveClientId($clientId)
    {
        $this->_saveConfig(self::API_CLIENT_ID, $clientId);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getClientSecret($store = null)
    {
        return $this->getConfigValue(self::API_CLIENT_SECRET, $store);
    }

    /**
     * @param $clientSecret
     */
    public function saveClientSecret($clientSecret)
    {
        $this->_saveConfig(self::API_CLIENT_SECRET, $clientSecret);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getRedirectUrl()
    {
        return $this->_storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_DIRECT_LINK)
            . "udeytech_instagram/api/connect";
    }

    /**
     * @return ReinitableConfigInterface
     */
    public function reinit()
    {
        return $this->_appConfig->reinit();
    }

}
