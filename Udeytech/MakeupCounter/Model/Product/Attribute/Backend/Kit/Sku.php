<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\MakeupCounter\Model\Product\Attribute\Backend\Kit;

use Exception;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\StateException;

/**
 * Class Sku
 * @package Udeytech\MakeupCounter\Model\Product\Attribute\Backend\Kit
 */
class Sku extends AbstractBackend
{
    /**
     * Validate Related Kit SKU
     * @param Magento\Catalog\Model\Product $object
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validate($object)
    {
        $objectManager = ObjectManager::getInstance();
        $productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        $collection = $productCollectionFactory->create();
        try {
            //$attrCode = $this->getAttribute()->getAttributeCode();
            //$sku = $object->getData($attrCode);
            $sku = "makeup-diary-austin";
            $productId = $collection->getIdBySku($sku);

        } catch (Exception $e) {

            throw new StateException(__('There is no product with the same SKU'));
        }
        return parent::validate($object);
    }
}
