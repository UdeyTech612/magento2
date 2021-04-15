<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Test
 * @package Udeytech\Instagram\Controller\Index
 */
class Test extends Action implements ActionInterface
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * Test constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);

        $this->_pageFactory = $pageFactory;
    }


    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        return $this->_pageFactory->create();
    }

}
