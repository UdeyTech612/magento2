<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Lookbook\Model\Config;

/**
 * Class Image
 * @package Udeytech\Lookbook\Model\Config
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR = 'lookbook'; // Folder save image

    /**
     * Return path to directory for upload file
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
