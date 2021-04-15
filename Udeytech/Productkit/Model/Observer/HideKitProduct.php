<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;
use Exception;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class HideKitProduct
 * @package Udeytech\Productkit\Model\Observer
 */
class HideKitProduct implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var Session
     */
    protected $_adminSession;

    /**
     * HideKitProduct constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $adminSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $adminSession
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_adminSession = $adminSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_scopeConfig->getValue('udeytech_productkit/udeytech_productkit/kit_product_save') > 0
            && null != $observer->getProduct()->getId()) {
            $product = $observer->getProduct();
            if ($product->getAttributeSetId() != $this->helper('Udeytech\Productkit\Helper\Data')->getAttributeSetId()
                && $product->getProductkitType() > 0 && $product->getData('visibility') != Visibility::VISIBILITY_NOT_VISIBLE
            ) {
                try {
                    $product->setData('visibility', Visibility::VISIBILITY_NOT_VISIBLE)->save();
                } catch (\Exception $e) {
                    $this->_adminSession->addError(
                        $this->helper('Udeyetch\Productkit\Helper\Data')->__('Could not hide product applied to productkit. Please, try again or hide it manually')
                    );
                }
            }
        }
    }
}
