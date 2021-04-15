<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Productkit\Model\Rewrite;
use Magento\Framework\Model\Context;
use Udeytech\Productkit\Model\Source\Kit\Type;

/**
 * Class CatalogProductOptionTypeText
 * @package Udeytech\Productkit\Model\Rewrite
 */
class CatalogProductOptionTypeText extends \Magento\Catalog\Model\Product\Option\Type\Text
{
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var Expert
     */
    protected $_expertProduct;
    /**
     * @var CollectionFacory
     */
    protected $_collectionFactory;

    /**
     * CatalogProductOptionTypeText constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Expert $expertProduct
     * @param CollectionFacory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Udeytech\Productkit\Model\Expert $expertProduct,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
    ){
         $this->_checkoutSession = $checkoutSession;
        $this->_expertProduct = $expertProduct;
        $this->_registry = $registry;
        $this->_collectionFactory = $collectionFactory;
        parent::_construct($context, $data);
    }

    /**
     * @param $optionValue
     * @param $basePrice
     * @return int|void
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $item = $this->getQuoteItemOption()->getItem();
        $sku = $item->getSku();
        if (!$this->_helper()->isKitProduct($sku) OR 'products' != $this->getOption()->getTitle()) {
            return parent::getOptionPrice($optionValue, $basePrice);
        }
        $name = $this->_helper()->getKitNameBySku($sku);
        $kitType = $this->_helper()->getKitType($name);
        if (Type::KIT_TYPE_CUSTOM == $kitType) {
            if ($item->getProductkitChooseId()) {
                return $this->_getCustomKitChoosePrice($item->getProductkitChooseId());
            }
            return $this->_getCustomKitOptionPrice($name);
        } else {
            return $this->_getFreeKitOptionPrice($name);
        }
    }

    /**
     * @return mixed
     */
    protected function _helper()
    {
        return $this->helper('Udeytech\Productkit\Helper\Data');
    }

    /**
     * @param $kitChooseId
     */
    protected function _getCustomKitChoosePrice($kitChooseId)
    {
        $kitChoose = $this->_expertProduct->load($kitChooseId);
        $price = $kitChoose->getPrice();
        if (!$price)
            $price = $this->_getCustomKitOptionPrice(Type::KIT_TYPE_CUSTOM);
        $price = $this->_helper()->preparePrice($price);
        return $price;
    }

    /**
     * @param $name
     */
    protected function _getCustomKitOptionPrice($name)
    {
                $kit = $this->_helper()->getKitByName($name);
                $discount = $kit->getData('productkit_kit_value');
                $selectedProductIds = unserialize($this->getQuoteItemOption()->getValue());
                $selectedProduct = $this->_collectionFactory->create();
                /* @var $selectedProduct \Magento\Catalog\Model\Product */
                $selectedTotalPrice = 0;
                foreach($selectedProductIds as $_productId){
                    $selectedProduct->load($_productId);
                    $selectedTotalPrice += $selectedProduct->getPrice();
                }
                if ($kit->getData('productkit_kit_price') == \Udeytech\Productkit\Model\Source\Kit\Type::KIT_PRICE_PERCENT){
                    $discount = round($discount / 100, 2);
                    $selectedDiscountPrice = $selectedTotalPrice * (1 - $discount);
                } else {
                    $selectedDiscountPrice = $discount;
                }
                if ($selectedDiscountPrice < 0) {
                    $selectedDiscountPrice = 0;
                }
                return $selectedDiscountPrice;
    }
    /**
     * @param $name
     * @return int
     */
    protected function _getFreeKitOptionPrice($name) {
        $kit = $this->_helper()->getKitByName($name);
        $item = $this->getQuoteItemOption()->getItem();
        $request = $item->getBuyRequest();
        $price = $request->getPrice();
        $price += 0;
        $minprice = $kit->getData('productkit_kit_value');
        if($price < $minprice) {
            $checkoutSession = $this->_checkoutSession;
            $checkoutSession->addNotice($this->helper('Udeytech\Productkit\Helper\Data')->__('Free kit price was reset from %.2f to the minimal allowed %.2f', $price, $minprice));
            $price = $minprice;
            $buyRequestOption = $item->getOptionByCode('info_buyRequest');
            $buyRequest = unserialize($buyRequestOption->getValue());
            $buyRequest['price'] = $price;
            $buyRequestOption->setValue(serialize($buyRequest));
        }
        $price = $this->_helper()->preparePrice($price);
        return $price;
    }
}
