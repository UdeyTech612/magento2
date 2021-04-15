<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class CheckOrderInventory
 * @package Udeytech\Productkit\Model\Observer
 */
class CheckOrderInventory implements ObserverInterface
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
     * CheckOrderInventory constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        SessionManagerInterface $coreSession
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_coreSession = $coreSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderParams = $observer->getEvent()->getData('controller_action')->getRequest()->getParams();
        $orderId = (int)$orderParams['order_id'];
        $objectManager = Magento\Framework\App\ObjectManager::getInstance();
        $orders = $objectManager->get('Magento\Sales\Model\Order')->getCollection();
        $order = $orders->load($orderId);
        $itemsOrder = $order->getAllItems();
        foreach ($itemsOrder as $item) {
            if ($this->helper("Udeytech\Productkit\Helper\Data")->isKitProduct($item->getSku())) {
                $prodoctOption = $item->getProductOptions();
                $jsonOption = $prodoctOption['info_buyRequest']['options'];
                foreach ($jsonOption as $jsonStr) {
                    $json = $jsonStr;
                }
                $arr = explode('"', $json);
                foreach ($arr as $idItems) {
                    if ((int)$idItems) {
                        $childProdKits[] = $idItems;
                    }
                }
                $error = false;
                foreach ($childProdKits as $childProduct) {
                    $kitItem = $this->_productCollectionFactory->create()->load($childProduct);
                    if (!$kitItem->getData('entity_id')) {
                        $error = true;
                    }
                }
                if ($error) {
                    $this->_coreSession->addError('Some of these products cannot be ordered - products are not available.');
                    $objectManager = ObjectManager::getInstance();
                    $redirect = $objectManager->get('\Magento\Framework\App\Response\Http');
                    $redirect->setRedirect($this->getUrl('customer/account'));
                    $redirect->getFrontController()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
    }
}
