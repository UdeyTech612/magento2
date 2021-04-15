<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Widget;

use Magento\Framework\Data\Collection;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Udeytech\Instagram\Helper\Config;
use Udeytech\Instagram\Model\Instagram;
use Udeytech\Instagram\Model\Source\Mode;

/**
 * @method string getIsEnabled()
 * @method string getMode()
 * @method string getUserId()
 * @method string getUserName()
 * @method string getHashtag()
 * @method string getLimitItems()
 * @method string getImageWidth()
 * @method string getImageHeight()
 */
class Feed extends Template implements BlockInterface
{
    /**
     *
     */
    const CACHE_KEY = 'UDEYTECH_WIDGET_INSTAGRAM_CACHE_KEY';

    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Instagram
     */
    protected $_api;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Config $configHelper
     * @param Instagram $modelInstagram
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Config $configHelper,
        Instagram $modelInstagram,
        Registry $registry,
        Context $context,
        array $data = []
    )
    {
        $this->_configHelper = $configHelper;
        $this->_api = $modelInstagram;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param string $name
     * @return Template
     */
    public function setNameInLayout($name)
    {
        $this->addData(
            [
                'cache_lifetime' => $this->_configHelper->getCacheLifetime(),
                'cache_key' => self::CACHE_KEY . '-' . $name,
            ]
        );
        return parent::setNameInLayout($name);
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        return $this->_configHelper->isConnected() && (
                $this->getIsEnabled() || $this->getIsEnabled() === null);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        $title = $this->getData('title');
        $hashtag = $this->getHashtag();
        if ($hashtag) {
            $title = str_replace('%s', $hashtag, $title);
        }
        return $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        $description = $this->getData('description');
        $hashtag = $this->getHashtag();
        if ($hashtag) {
            $description = str_replace('%s', $hashtag, $description);
        }
        return $description;
    }

    /**
     * @return array|Collection
     */
    public function getImageList()
    {
        switch ($this->getMode()) {
            case Mode::BY_USER_ID_CODE:
                return $this->_getUserMediaById();
            case Mode::BY_HASHTAG_CODE:
                return $this->_getTagMedia();
            case Mode::BY_PRODUCT_HASHTAG_CODE:
                return $this->_getProductTagMedia();
            case Mode::BY_USER_NAME_CODE:
                return $this->_getUserMediaByName();
            default:
                $imageList = [];
                break;
        }
        return $imageList;
    }

    /**
     * @return array|Collection
     */
    protected function _getUserMediaById()
    {
        return $this->_api->getUserMediaById(
            $this->getUserId(), $this->getLimitItems()
        );
    }

    /**
     * @return array|Collection
     */
    protected function _getTagMedia()
    {
        if (!$this->getHashtag()) {
            return [];
        }
        return $this->_api->getTagMedia(
            $this->getHashtag(), $this->getLimitItems()
        );
    }

    /**
     * @return array|Collection
     */
    protected function _getProductTagMedia()
    {
        $product = $this->_getProduct();
        if (!$product || !$product->getId() || !$product->getInstagramHashtag()) {
            return [];
        }
        $this->setHashtag($product->getInstagramHashtag());
        return $this->_api->getTagMedia(
            $this->getHashtag(), $this->getLimitItems()
        );
    }

    /**
     * @return Product
     */
    protected function _getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return array|Collection
     */
    protected function _getUserMediaByName()
    {
        return $this->_api->getUserMediaByName(
            $this->getUserName(), $this->getLimitItems()
        );
    }
}
