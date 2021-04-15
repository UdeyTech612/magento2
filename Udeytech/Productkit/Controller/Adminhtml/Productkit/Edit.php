<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Adminhtml\Productkit;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Model\Productkit;

/**
 * Class Edit
 * @package Udeytech\Productkit\Controller\Adminhtml\Productkit
 */
class Edit extends \Udeytech\Productkit\Controller\Adminhtml\Productkit
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('productkit_id');
        $model = $this->_objectManager->create(Productkit::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Productkit no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('udeytech_productkit_productkit', $model);

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Productkit') : __('New Productkit'),
            $id ? __('Edit Productkit') : __('New Productkit')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Productkits'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Productkit %1', $model->getId()) : __('New Productkit'));
        return $resultPage;
    }
}

