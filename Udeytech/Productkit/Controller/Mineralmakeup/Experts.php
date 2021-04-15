<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Controller\Mineralmakeup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\Productkit\Helper\Data;

/**
 * Class Experts
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Experts extends Action
{
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Experts constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!isset($data['type']) || $data['type'] == '') {
                $data['type'] = 'custom';
            }
            $kitType = $this->_helper->getKitType($data['type']);
            $this->_initAction($data['type'], $kitType);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirectUrl($this->_getRefererUrl());
        }
    }
}
