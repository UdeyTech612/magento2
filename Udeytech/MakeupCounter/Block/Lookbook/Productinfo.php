<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Block\Lookbook;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Productinfo
 * @package Udeytech\MakeupCounter\Block\Lookbook
 */
class Productinfo extends Template
{
    /**
     * @var
     */
    protected $productCollectionFactory;

    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getUrl('makeupcounter/index');
    }

    /**
     * @return /Magento/Catalog/Model/Product
     */
    public function getProduct()
    {
        $objectManager = ObjectManager::getInstance();
        $productCollection = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
        return $productCollection;
    }
}

