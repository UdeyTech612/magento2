<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Mineralmakeup;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Helper\Data;

/**
 * Class Products
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Products extends Action
{
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var ProductFactory
     */
    protected $_categoryFactory;

    /**
     * Products constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param ProductFactory $categoryFactory
     * @param \Udeytech\FreeSamplePage\Helper\Data $freeSampleHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        ProductFactory $categoryFactory,
        \Udeytech\FreeSamplePage\Helper\Data $freeSampleHelper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_freeSampleHelper = $freeSampleHelper;
        $this->_helper = $helper;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type', '');
        if (empty($type)) {
            $this->getResponse()->setBody(__('Invalid request: makeup type is empty'));
            return;
        }
        $kitType = $this->_helper->getKitType($type);
        if (!$kitType) {
            $this->getResponse()->setBody(__('Invalid request: kit have invalid type'));
            return;
        }
        $categoryId = $this->getRequest()->getParam('category', '');
        $categoryPosition = $this->getRequest()->getParam('categoryPosition', false);
        $kitId = $this->getRequest()->getParam('kitId', false);
        $category = $this->_categoryFactory->create()->load($categoryId);
        if (!$category->getId()) {
            $this->getResponse()->setBody(__('Invalid request: category is empty'));
            return;
        }
        $this->_registry->register('current_category', $category);
        $this->_registry->register('current_category_position', $categoryPosition);
        $this->_registry->register('current_kit_id', $kitId);
        $this->_initAction($type, $kitType);
        $block = $this->loadLayout()->getLayout()->getBlock('root');
        $this->getResponse()->setBody($block->toHtml());
    }
}
