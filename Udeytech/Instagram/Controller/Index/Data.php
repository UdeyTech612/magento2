<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CacheInterface;
use Udeytech\Instagram\Helper\Config;

/**
 * Class Data
 * @package Udeytech\Instagram\Controller\Index
 */
class Data extends Action
{
    /**
     *
     */
    const CACHE_KEY = 'udeytech_instagram_';

    /**
     *
     * @var Config
     */
    protected $_cfgHelp;

    /**
     * Data constructor.
     * @param Context $context
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    )
    {
        parent::__construct($context);
        $this->_cfgHelp = $config;
    }

    /**
     * data gate
     */
    public function execute()
    {
        $path = $this->getRequest()->getParam('path');

        if (!empty($path)) {
            $key = self::CACHE_KEY . md5($path);

            /* @var CacheInterface $cache */
            $cache = $this->_objectManager->get('\Magento\Framework\App\CacheInterface');
            $data = $cache->load($key);
            if ($data) {
                $data = unserialize($data);
            } else {

                if (@$data = file_get_contents($path)) {
                    $data = json_decode($data);
                    if ($data) {
                        $cache->save(serialize($data), $key, [], $this->_cfgHelp->getCacheLifetime());
                    }
                }
            }
        } else {
            $data = null;
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($data));
    }

}
