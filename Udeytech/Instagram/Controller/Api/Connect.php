<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Api;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Udeytech\Instagram\Controller\Adminhtml\Api as AbstrCtrl;

/**
 * Class Connect
 * @package Udeytech\Instagram\Controller\Api
 */
class Connect extends AbstrCtrl
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('code', null);
        if ($code === null) {
            $this->_configHelper->disconnect();
            $this->messageManager->addError(
                __('Incorrect Instagram authorization code.'));
        } else {
            try {
                $accessToken = $this->_getApi()->getOAuthToken($code, true);
            } catch (Exception $e) {
                $this->_configHelper->disconnect();
                $this->messageManager->addError(__($e->getMessage()));
            }

            if (!$accessToken) {
                $this->_configHelper->disconnect();
                $this->messageManager->addError(
                    __('Incorrect Instagram authorization code.'));
            }

            $this->_configHelper->connect($accessToken);
            $this->messageManager->addSuccess(
                __('Instagram connect is successful.'));
        }
        $this->_configHelper->reinit();
        return $this->_resultPageFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
        //return $this->_authorization->isAllowed('Udeytech_Instagram::api_connect');
    }
}
