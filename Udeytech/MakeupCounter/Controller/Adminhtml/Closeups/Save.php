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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Save
 * @package Udeytech\MakeupCounter\Controller\Adminhtml\Closeups
 */
class Save extends Action
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
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param RedirectInterface $redirect
     * @param ProductRepositoryInterface $productRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        RedirectInterface $redirect,
        ProductRepositoryInterface $productRepository,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_registry = $registry;
        $this->redirect = $redirect;
        $this->logger = $logger;
        $this->_collectionFactory = $collectionFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
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
        $product = $this->_productRepository->getById($productId);
        if (!$product->getId()) {
            return;
        }
        $attr = Data::LOOKBOOK_CLOSEUPS_ATTR;
        $closeups = $product->getData($attr);
        //$closeups[$closeup['id']] = $closeup;
        //$product->setData($attr, $closeups);
        try {
            $product->save();
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), null, 'udeytech_makup_counter.log', true);
        }
        $this->getResponse()->setBody('Closeup Save Successfuly!');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->redirect->getRefererUrl());
        return $resultRedirect;
    }
}

