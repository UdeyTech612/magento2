<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

//use Magento\Framework\Registry;

/**
 * Class SalesQuoteItemSetCustomAttribute
 * @package Udeytech\Productkit\Model\Observer
 */
class SalesQuoteItemSetCustomAttribute implements ObserverInterface
{
    protected $_kitCollection;
    // protected $registry;
    /**
     * SalesQuoteItemSetCustomAttribute constructor.
     * @param Collection $kitCollection
     */
    /**
     * @param Observer $observer
     */
        public function __construct(
            \Magento\Framework\Registry $registry,
            \Udeytech\Productkit\Model\ResourceModel\Productkit\CollectionFactory $productCollectionFactory
           ){
            $this->_productCollectionFactory = $productCollectionFactory;
            //$this->registry = $registry;
           }
    public function execute(\Magento\Framework\Event\Observer $observer) {
         $quoteItem = $observer->getQuoteItem();
         $product = $observer->getProduct();
        //if(!$this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($product->getSku())) {
        //    return;
        //  }
         $kitChooseId = $quoteItem->getProductkitChooseId();
        if ($kitChooseId) {
            $_kitChoose = $this->_kitCollection->create()->load($kitChooseId);
            if (!$_kitChoose->getId())
                return;
            $quoteItem->setName($_kitChoose->getKitChooseTitle());
            $quoteItem->setProductkitChooseId($kitChooseId);
        }
        $quoteItem->setName('Free Sample Kit');
    }
}
