<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Controller\Adminhtml\Questions;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Udeytech\FreeSampleQuiz\Model\Questions;

/**
 * Class Delete
 * @package Udeytech\FreeSampleQuiz\Controller\Adminhtml\Questions
 */
class Delete extends \Udeytech\FreeSampleQuiz\Controller\Adminhtml\Questions
{

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('questions_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(Questions::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Questions.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['questions_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Questions to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

