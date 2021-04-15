<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Adminhtml\Productkit;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Udeytech\Productkit\Model\Productkit;

/**
 * Class Save
 * @package Udeytech\Productkit\Controller\Adminhtml\Productkit
 */
class Save extends Action
{

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        UploaderFactory $fileUploaderFactory
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->_fileUploaderFactory = $fileUploaderFactory;
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
            if (isset($data['logo'][0]['name']) && isset($data['logo'][0]['tmp_name'])) {
                $data['logo'] = $data['logo'][0]['name'];
                $this->imageUploader = ObjectManager::getInstance()->get(
                    'Udeytech\Productkit\HelloWorldImageUpload'
                );
                $data1['image'] = $data['logo'];
                $this->imageUploader->moveFileFromTmp($data1['image']);
            } elseif (isset($data['logo'][0]['image']) && !isset($data['logo'][0]['tmp_name'])) {
                $data['logo'] = $data['logo'][0]['image'];
            } else {
                $data['logo'] = null;
            }
            if (isset($data['products'])) {
                $i = 1;
                foreach ($data['products'] as $newdata) {
                    $dataa[$i] = $newdata['entity_id'];
                    $i++;
                }
                $data['selected_products'] = implode(',', $dataa);
            }
            if (isset($data['product_category_ids'])) {
                $data['selected_category'] = implode(',', $data['product_category_ids']);
            }
            if (isset($data['productkit_id'])) {
                $data = array(
                    'productkit_id' => $data['productkit_id'],
                    'kit_type' => $data['kit_type'],
                    'kit_choose_title' => $data['kit_choose_title'],
                    'status' => $data['status'],
                    'description' => $data['description'],
                    'form_key' => $data['form_key'],
                    'price' => $data['price'],
                    'logo' => $data['logo'],
                    'selected_products' => $data['selected_products'],
                    'selected_category' => $data['selected_category']);
            } else {
                $data = array(
                    'kit_type' => $data['kit_type'],
                    'kit_choose_title' => $data['kit_choose_title'],
                    'status' => $data['status'],
                    'description' => $data['description'],
                    'form_key' => $data['form_key'],
                    'price' => $data['price'],
                    'logo' => $data['logo'],
                    'selected_products' => $data['selected_products'],
                    'selected_category' => $data['selected_category']);
            }
            $id = $this->getRequest()->getParam('productkit_id');
            $model = $this->_objectManager->create(Productkit::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Productkit no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Productkit.'));
                $this->dataPersistor->clear('udeytech_productkit_productkit');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['productkit_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Productkit.'));
            }
            $this->dataPersistor->set('udeytech_productkit_productkit', $data);
            return $resultRedirect->setPath('*/*/edit', ['productkit_id' => $this->getRequest()->getParam('productkit_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

