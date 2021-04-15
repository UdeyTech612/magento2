<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\MakeupCounter\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Index
 * @package Udeytech\MakeupCounter\Controller\Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    protected $_helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    /**
     * Execute view action
     * @return ResultInterface
     */
    public function execute(){
        $lookBookCollection = $this->_helper->getLookbookCollection();
        $lastLookBookUrl = $lookBookCollection->getLastItem()->getProductUrl();
        $this->_redirectUrl($lastLookBookUrl);
        //return $this->resultPageFactory->create();
     }
}

