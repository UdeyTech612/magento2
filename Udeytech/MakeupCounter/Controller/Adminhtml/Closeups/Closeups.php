<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Controller\Adminhtml\Closeups;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Closeups
 * @package Udeytech\MakeupCounter\Controller\Adminhtml\Closeups
 */
class Closeups extends Action
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
     * Constructor
     * @param Context $context
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
     * Execute view action
     * @return ResultInterface
     */
    public function execute()
    {
//        $params = $this->getRequest()->getParams();
//        $productId = $params['product_id'];
//        $closeupId = $params['image_id'];
//        $product = $this->_collectionFactory->create()->load($productId);
//        if (!$product->getId()){
//            return;
//        }
//        $this->_registry->registry('current_product', $product);
//        $this->_registry->registry('current_closeup_id', $closeupId);
//        $this->loadLayout();
//        $this->renderLayout();
        return $this->resultPageFactory->create();
    }

    /**
     *
     */
    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];
        $closeup = $params['closeup'];
        if (!isset($closeup['day'])) {
            $closeup['day'] = 0;
        }
        if (!isset($closeup['night'])) {
            $closeup['night'] = 0;
        }
        $product = $this->_collectionFactory->create()->load($productId);
        if (!$product->getId()) {
            return;
        }
        $attr = Data::LOOKBOOK_CLOSEUPS_ATTR;
        $closeups = $product->getData($attr);
        $closeups[$closeup['id']] = $closeup;
        $product->setData($attr, $closeups);
        try {
            $product->save();
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), null, 'udeytech_makup_counter.log', true);
        }
        $this->getResponse()->setBody('Closeup save successfuly!');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}

