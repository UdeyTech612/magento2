<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Controller\Adminhtml\Closeups;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Index
 * @package Udeytech\MakeupCounter\Controller\Adminhtml\Closeups
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_registry = $registry;
        $this->logger = $logger;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];
        $closeupId = $params['image_id'];
        $product = $this->_collectionFactory->create()->load($productId);
        if (!$product->getId()) {
            return;
        }
        $this->_registry->registry('current_product', $product);
        $this->_registry->registry('current_closeup_id', $closeupId);
        $this->loadLayout();
        $this->renderLayout();
        //return $this->resultPageFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}

