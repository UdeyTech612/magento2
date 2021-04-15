<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\App\ObjectManager;
use Udeyetch\Productkit\Helper\Rewrite;
/**
 * Class CatalogProductConfiguration
 */
class CatalogProductConfiguration extends Magento\Catalog\Helper\Product\Configuration
{
    /**
     * @param ItemInterface $item
     * @return array
     */
    public function getCustomOptions(ItemInterface $item)
    {
        if ($this->helper('Udeytech\Totalcustomization\Helper\Data')->isKitProduct($item)) {
            return parent::getCustomOptions($item);
        }
        $product = $item->getProduct();
        $options = array();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option AND 'products' == $option->getTitle()) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $selectedProductIds = unserialize($itemOption->getValue());
                    $objectManager = ObjectManager::getInstance();
                    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                    $selectedProduct = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
                    if ($item->isItemOptionsHasPrice()) {
                        foreach ($selectedProductIds as $_productId) {
                            $selectedProduct->load($_productId);
                            $_formatedPrice = $storeManager->getStore($item->getQuote()->getStoreId())->formatPrice($selectedProduct->getPrice());
                            $_option = array(
                                'label' => $selectedProduct->getName(),
                                'value' => $_formatedPrice,
                                'print_value' => $_formatedPrice,
                                'option_id' => $option->getId(),
                                'option_type' => 'field',
                            );
                            $options[] = $_option;
                        }
                        } else {
                        foreach ($selectedProductIds as $_productId) {
                            $selectedProduct->load($_productId);
                            $_option = array(
                                'label' => $selectedProduct->getName(),
                                'value' => '-',
                                'print_value' => '-',
                                'option_id' => $option->getId(),
                                'option_type' => 'field',
                            );
                            $options[] = $_option;
                        }
                    }
                }
            }
        }
        return $options;
    }
}
