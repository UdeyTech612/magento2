<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSamplePage\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;

/**
 * Class Data
 * @package Udeytech\FreeSamplePage\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const FSP_ROOT_CATEGORY_URL = 'free-sample';
    /**
     * @var ScopeConfigInterface
     */
    protected $config;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    )
    {
        $this->config = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @param null $productId
     * @return mixed
     */
    public function getFspBundleProduct($productId = null)
    {
        $objectManager = ObjectManager::getInstance();
        if ($productId === null) {
            $productId = $this->getFspBundleProductID();
        }
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if ($product->getTypeId() != Type::TYPE_BUNDLE) {
            $product = $objectManager->get($this->getFspBundleProductID());
        }
        return $product;

    }

    /**
     * Retrieving product ID from config
     * @return int
     */
    public function getFspBundleProductID()
    {
        return $this->config->getValue('freesamplepage/general/4free_sample_bundle_product');
    }

    /**
     * @param null $productId
     * @return mixed
     */
    public function getFspBundleProduct1($productId = null)
    {
        $objectManager = ObjectManager::getInstance();
        if ($productId === null) {
            $productId = $this->getFspBundleProductID1();
        }
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if ($product->getTypeId() != Type::TYPE_BUNDLE) {
            $product = $objectManager->get($this->getFspBundleProductID1());
        }
        return $product;
    }

    /**
     * @return mixed
     */
    public function getFspBundleProductID1()
    {
        return $this->config->getValue('freesamplepage/general/7free_sample_bundle_product');
    }
}
