<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\FreeSampleQuiz\Controller\Adminhtml\Answers;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Udeytech\FreeSampleQuiz\Model\Answers;

/**
 * Class Save
 * @package Udeytech\FreeSampleQuiz\Controller\Adminhtml\Answers
 */
class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    )
    {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data['thumb'][0]['name']) && isset($data['thumb'][0]['tmp_name'])) {
                $data['thumb'] = $data['thumb'][0]['name'];
                $this->imageUploader = ObjectManager::getInstance()->get(
                    'Udeytech\FreeSampleQuiz\HelloWorldImageUpload'
                );
                $data1['image'] = $data['thumb'];
                $this->imageUploader->moveFileFromTmp($data1['image']);
            } elseif (isset($data['thumb'][0]['image']) && !isset($data['thumb'][0]['tmp_name'])) {
                $data['thumb'] = $data['thumb'][0]['image'];
            } else {
                $data['thumb'] = null;
            }
            if (isset($data['associated_codes'])) {
                $data['associated_codes'] = implode(',', $data['associated_codes']);
            }
            $id = $this->getRequest()->getParam('answers_id');
            $model = $this->_objectManager->create(Answers::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Answers no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Answers.'));
                $this->dataPersistor->clear('udeytech_freesamplequiz_answers');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['answers_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Answers.'));
            }
            $this->dataPersistor->set('udeytech_freesamplequiz_answers', $data);
            return $resultRedirect->setPath('*/*/edit', ['answers_id' => $this->getRequest()->getParam('answers_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

