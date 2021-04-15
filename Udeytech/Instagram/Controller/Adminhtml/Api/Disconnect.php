<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Adminhtml\Api;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Udeytech\Instagram\Controller\Adminhtml\Api as AbstrCtrl;

/**
 * Class Disconnect
 * @package Udeytech\Instagram\Controller\Adminhtml\Api
 */
class Disconnect extends AbstrCtrl
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->_configHelper->disconnect();
        $this->messageManager->addSuccess(
            __('Instagram disconnect is successful.'));
        $this->_configHelper->reinit();
        return $this->_redirect('adminhtml/system_config/edit',
            ['section' => 'udeytech_instagram']);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Udeytech_Instagram::api_disconnect');
    }
}
