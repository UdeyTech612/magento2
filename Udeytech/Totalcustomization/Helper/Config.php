<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Totalcustomization\Helper;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Udeytech\Totalcustomization\Helper
 */
class Config extends AbstractHelper
{
    /**
     *
     */
    const XPATH_ALREADY_IN_CART_MSG = 'freesamplequiz/total_customization/freekit_already_in_cart_message';
    /**
     *
     */
    const XPATH_BANNER_IMG = 'freesamplequiz/total_customization/bannerimg';
    /**
     *
     */
    const DEFAULT_BANNER_IMG_PATH = 'images/total-customization-banner-default.jpg';
    /**
     * @param Context $context
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Page
     */
    protected $_page;

    /**
     * Config constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Page $page
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Page $page
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_page = $page;
        parent::__construct($context);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBannerImgSrc()
    {
        $configuredImage = $this->scopeConfig->getValue(self::XPATH_BANNER_IMG);
        if ($configuredImage) {
            return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::XPATH_BANNER_IMG . '/' . $configuredImage;
        } else {
            $defaultImagePath = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . '/' . self::DEFAULT_BANNER_IMG_PATH;
            if (file_exists($defaultImagePath) && is_readable($defaultImagePath)) {
                return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . '/' . self::DEFAULT_BANNER_IMG_PATH;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAlreadyInCartMessage()
    {
        return $this->scopeConfig->getValue(static::XPATH_ALREADY_IN_CART_MSG);
    }

    /**
     * @return string
     */
    public function getCmsTitle()
    {
        $page = $this->_page->getTitle();
        return $page;
    }
}

