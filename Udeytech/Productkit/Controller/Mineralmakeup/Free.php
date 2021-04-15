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
use Udeytech\FreeSamplePage\Helper\Data;
/**
 * Class Free
 * @package Udeytech\Productkit\Controller\Mineralmakeup
 */
class Free extends Action {
    /**
     * @var Data
     */
    protected $_freeSampleHelper;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * Free constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $freeSampleHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $freeSampleHelper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_freeSampleHelper = $freeSampleHelper;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute(){
        $fspBundleProduct = $this->_freeSampleHelper->getFspBundleProduct();
        if ($fspBundleProduct) {
            $this->_redirectUrl($fspBundleProduct->getProductUrl());
        } else {
            return $this->_redirectUrl('https://magento-431466-1352594.cloudwaysapps.com');
        }
    }
}
