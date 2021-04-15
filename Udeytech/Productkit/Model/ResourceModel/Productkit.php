<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Productkit\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Productkit
 * @package Udeytech\Productkit\Model\ResourceModel
 */
class Productkit extends AbstractDb
{

    /**
     * Exclude expert choose items with zero products from collection,
     * Collection is ready for use with loaded products after this
     *
     * @return $this
     */
    public function validateChoosesProducts()
    {
        $collectionItems = $this->getItems();
        /** @var /Udeytech/Productkit/Model/Expert $item */
        foreach ($collectionItems as $key => $item) {
            $productIds = $item->getRecommendedProductIds();
            /** @var /Magento/Catalog/Model/Resource/Product/Collection $productCollection */
            $objectManager = ObjectManager::getInstance();
            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->setStoreId($objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getStoreId())
                ->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in' => $productIds,));
            $productCollection->joinField('catalog/product_status')->addVisibleFilterToCollection($productCollection);
            $productCollection->joinField('catalog/product_status')->addSaleableFilterToCollection($productCollection);
            $productCollection->joinField('cataloginventory/stock_status')->addIsInStockFilterToCollection($productCollection);
            if ($productCollection->getSize() == 0) {
                $this->removeItemByKey($key);
            } else {
                $products = $productCollection->getItems();
                $products = $this->_sortLoadedProducts($item, $products);
                $item->setData('products', $products);
            }
        }
        return $this;
    }

    /**
     * @param $item /Udeytech/Productkit/Model/xpert
     * @param $products /Magento/Catalog/Model/Product[]
     */
    protected function _sortLoadedProducts($item, $products)
    {
        uasort($products, function ($a, $b) use ($item) {
            $aPosition = $item->getRecommendedProductPosition($a->getId());
            $bPosition = $item->getRecommendedProductPosition($b->getId());
            if ($aPosition == $bPosition) {
                return 0;
            }
            return ($aPosition < $bPosition) ? -1 : 1;
        });
        return $products;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('udeytech_productkit_productkit', 'productkit_id');
    }
}

