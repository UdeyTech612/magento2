<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Observer;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class updateOrderItemOptions
 * @package Udeytech\Productkit\Model\Observer
 */
class UpdateOrderItemOptions implements ObserverInterface
{

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var Stock
     */
    protected $_stockFilter;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * updateOrderItemOptions constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param Stock $stockFilter
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Stock $stockFilter,
        StoreManagerInterface $storeManager,
        Registry $registry)
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockFilter = $stockFilter;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
    }
    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();
        $product = $quoteItem->getProduct();
       // if($this->helper('Udeytech\Productkit\Helper\Data')->isKitProduct($product->getSku())) {
            $productOptions = $product->getOptions();
            $kitOptionId = 0;
            foreach ($productOptions as $_option) {
                if ('products' == $_option->getTitle()) {
                    $kitOptionId = $_option->getId();
                    break;
                }
            }
            $orderProductOptions = $orderItem->getProductOptions();
            if ($kitOptionId AND isset($orderProductOptions['info_buyRequest']['options'][$kitOptionId])) {
                foreach ($orderProductOptions['options'] as $_key => $productOption) {
                    if ($productOption['option_id'] == $kitOptionId) {
                        unset($orderProductOptions['options'][$_key]);
                        break;
                    }
                }
                $selectedProductIds = unserialize($orderProductOptions['info_buyRequest']['options'][$kitOptionId]);
                $selectedProduct = $this->_productCollectionFactory->create();
                foreach ($selectedProductIds as $_productId) {
                    $selectedProduct->load($_productId);
                    $_formatedPrice = $this->_storeManager->getStore($quoteItem->getQuote()->getStoreId())->formatPrice($selectedProduct->getPrice(), false);
                    $_formatedPrice = ($_formatedPrice == '$0.00') ? '-' : $_formatedPrice;
                    $orderProductOptions['options'][] = array(
                        'label' => $selectedProduct->getName(),
                        'value' => $_formatedPrice,
                        'print_value' => $_formatedPrice,
                        'option_id' => $kitOptionId,
                        'option_type' => 'field',
                    );
                }
                $orderItem->setProductOptions($orderProductOptions);
            }
        }
   // }
}
