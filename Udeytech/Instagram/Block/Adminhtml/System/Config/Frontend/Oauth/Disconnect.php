<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth;


use Magento\Backend\Block\Widget\Button;

/**
 * Class Disconnect
 * @package Udeytech\Instagram\Block\Adminhtml\System\Config\Frontend\Oauth
 */
class Disconnect extends AbstractBlock
{
    /**
     * @return Button
     */
    public function getButton()
    {
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setLabel(__('Disconnect'))
            ->setStyle("width:280px")
            ->setId('udeytech_instagram_oauth')
            ->setClass('delete');
        return $button;
    }

    /**
     * @return string
     */
    public function getDisconnectUrl()
    {
        return $this->getUrl("udeytech_instagram/api/disconnect");
    }

}
