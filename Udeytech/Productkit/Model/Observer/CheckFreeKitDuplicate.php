<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Productkit\Model\Observer;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Udeytech\Productkit\Helper\Data;
/**
 * Class CheckOrderInventory
 * @package Udeytech\Productkit\Model\Observer
 */
class CheckFreeKitDuplicate implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;
    /**
     * CheckFreeKitDuplicate constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        SessionManagerInterface $coreSession
    ){
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_coreSession = $coreSession;
    }
    /**
     * @param Observer $observer
     * @return Observer|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $kitItems = array();
        $quote = $observer->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($item->getSku() === Data::FREE_KIT_SKU) {
                $kitItems[] = $item->getItemId();
                if ($item->getQty() > 1) {
                    $item->setQty(1);
                }
            }
        }
        if (count($kitItems) > 1) {
            array_pop($kitItems);
            foreach ($kitItems as $duplicateKitId) {
                $quote->removeItem($duplicateKitId);
            }
        }
        return $observer;
    }
}
