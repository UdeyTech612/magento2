<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Adminhtml\Api;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Udeytech\Instagram\Controller\Adminhtml\Api as AbstrCtrl;

/**
 * Class Save
 * @package Udeytech\Instagram\Controller\Adminhtml\Api
 */
class Save extends AbstrCtrl
{

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = array(
            'success' => true,
            'login_url' => null,
        );
        $clientId = $this->getRequest()->getParam('client_id', null);
        $clientSecret = $this->getRequest()->getParam('client_secret', null);

        try {
            $this->_configHelper->saveClientId($clientId);
            $this->_configHelper->saveClientSecret($clientSecret);

            if (!$this->_configHelper->isConnected()) {
                $result['error'] = true;
            }
        } catch (Exception $e) {
            $result['success'] = false;
        }

        $result['login_url'] = $this->_getApi()->getLoginUrl(
            ['basic', 'public_content']);
        $this->_configHelper->reinit();
        return $this->_resultJsonFactory->create()->setData($result);
    }
}
