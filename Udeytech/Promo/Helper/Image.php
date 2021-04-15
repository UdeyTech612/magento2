<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\Promo\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
//use Magento\Framework\Backend\Session;
use Magento\Framework\Model\Store;
use Magento\Framework\UrlInterface;

/**
 * Class Image
 * @package Udeytech\Promo\Helper
 */
class Image extends AbstractHelper {
    /**
     * @var
     */
    protected $urlInterface;
    /**
     * @var
     */
    protected $_backendSession;

    /**
     * Image constructor.
     * @param Context $context
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
       //Session $backendSession,
        UrlInterface $urlInterface){
        $this->_urlInterface = $urlInterface;
       //$this->_backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @return string|null
     */
    function upload($field){
        $ret = null;
        try {
            $uploader = new Varien_File_Uploader($field);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setFilesDispersion(false);
            $uploader->setAllowRenameFiles(false);
            $path = $_FILES[$field]['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $fileName = uniqid($field . "_") . "." . $ext;
            $uploader->save($this->_getPath(), $fileName);
            $ret = $fileName;
        } catch (\Exception $e) {
            //$this->_backendSession->addError($e->getMessage());
        }
        return $ret;
    }

    /**
     * @return string
     */
    protected function _getPath(){
        return $this->_urlInterface->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . '/ampromo/';
    }

    /**
     * @param $file
     * @return string|null
     */
    function getLink($file)
    {
        return $file ? $this->_urlInterface->getBaseUrl(Store::URL_TYPE_MEDIA) . 'ampromo/' . $file : null;
    }
}
