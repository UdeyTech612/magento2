<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
namespace Udeytech\Promo\Block\Items;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Tax\Model\TaxCalculation;

/**
 * Class Item
 * @package Udeytech\Promo\Block\Items
 */
class Item extends Template {
    /**
     * @var string
     */
    protected $_template = 'udeytech/ampromo/items/item.phtml';
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var TaxCalculation
     */
    protected $_taxCalculation;
    /**
     * @var
     */
    protected $_texHelper;
    /**
     * @var ResolverInterface
     */
    protected $localeResolver;
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Item constructor.
     * @param Context $context
     * @param Image $productImageHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param TaxCalculation $taxCalculation
     * @param Registry $registry
     * @param ResolverInterface $localeResolver
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Image $productImageHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        TaxCalculation $taxCalculation,
       // \Magento\Core\Helper\Data $texHelper,
        Registry $registry,
        ResolverInterface $localeResolver,
        PriceCurrencyInterface $priceCurrency,
        array $data = []){
        $this->_jsonHelper = $jsonHelper;
      //  $this->_texHelper = $texHelper;
        $this->_registry = $registry;
        $this->localeResolver = $localeResolver;
        $this->_taxCalculation = $taxCalculation;
        $this->_productImageHelper = $productImageHelper;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFormActionUrl(){
        $params = $this->helper('Udeytech\Promo\Helper\Data')->getUrlParams();
        return $this->getUrl('ampromo/cart/update', $params);
    }

    /**
     * @param $product
     * @param bool $displayMinimalPrice
     * @param string $idSuffix
     * @return mixed|string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = ''){
        if($product->getAmpromoShowOrigPrice()) {
            if($product->getTypeId() == 'giftcard') {
            }
            $html = parent::getPriceHtml($product, $displayMinimalPrice, $idSuffix);
            if ($product->getSpecialPrice() == 0) {
                $html = str_replace('regular-price', 'old-price', $html);
            }
            return $html;
        }
        return '';
    }
    /**
     *
     */
    public function getJsonConfig() {

    }
    /**
     * @return string
     */
    public function getJsonConfigOld(){
        $config = array();
        if (!$this->hasOptions()) {
            return $this->_jsonHelper->jsonEncode($config);
        }
        $_request = $this->_taxCalculation->getRateRequest(false, false, false);
        $product = $this->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $_request->setProductClassId($product->getTaxClassId());
        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        if ($product->getTypeId() == Type::TYPE_BUNDLE) {
            } else {
        }
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = $this->_priceCurrency->currency($tierPrice['website_price'], false, false);
             $config = array(
                'productId' => $product->getId(),
                'priceFormat' => $this->localeResolver->getLocale()->getJsPriceFormat(),
                'productPrice' => $this->_priceCurrency->currency($_finalPrice, false, false),
                'productOldPrice' => $this->_priceCurrency->currency($_regularPrice, false, false),
                'defaultTax' => '0',
                'currentTax' => '0',
                'idSuffix' => '_clone',
                'oldPlusDisposition' => 0,
                'plusDisposition' => 0,
                'plusDispositionTax' => 0,
                'oldMinusDisposition' => 0,
                'minusDisposition' => 0,
                'tierPrices' => $_tierPrices,
                'tierPricesInclTax' => $_tierPricesInclTax,
            );
            $responseObject = new \Magento\Framework\DataObjects();
            $this->_eventManager->dispatch('catalog_product_view_config', array('response_object' => $responseObject));
            if (is_array($responseObject->getAdditionalOptions())) {
                foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                    $config[$option] = $value;
                }
            }
            return $this->_jsonHelper->jsonEncode($config);
        }
    }

    /**
     * @return bool
     */
    public function hasOptions(){
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTypeOptionsHtml()
    {
        $product = $this->getProduct();
        if ($this->_registry->registry('current_product')) {
            $this->_registry->unregister('current_product');
        }
        $this->_registry->register('current_product', $product);
        switch ($product->getTypeId()) {
            case 'downloadable':
                $_blockOpt = 'downloadable/catalog_product_links';
                $_templateOpt = 'udeytech/ampromo/items/downloadable.phtml';
                break;
            case 'configurable':
                $_blockOpt = 'catalog/product_view_type_configurable';
                $_templateOpt = 'udeytech/ampromo/items/configurable.phtml';
                break;
            case 'bundle':
                $_blockOpt = 'udeytceh/items_bundle';
                $_templateOpt = 'bundle/catalog/product/view/type/bundle/options.phtml';
                break;
            case 'amgiftcard':
                $_blockOpt = 'amgiftcard/catalog_product_view_type_giftCard';
                $_templateOpt = 'udeytech/amgiftcard/catalog/product/view/type/giftcard.phtml';
                break;
            case 'virtual':
                $_blockOpt = 'catalog/product_view_type_virtual';
                break;
            case 'giftcard':
                $_blockOpt = 'enterprise_giftcard/catalog_product_view_type_giftcard';
                $_templateOpt = 'udeytech/ampromo/items/giftcard.phtml';
                break;
            case 'amstcred':
                $_blockOpt = 'amstcred/catalog_product_view_type_storeCredit';
                $_templateOpt = 'udeytech/amstcred/catalog/product/view/type/amstcred.phtml';
                break;
        }
        if (!empty($_blockOpt) && !empty($_templateOpt)) {
            $block = $this->getLayout()->createBlock($_blockOpt, 'ampromo_item_' . $product->getId(), array('product' => $product))
                ->setProduct($product)->setTemplate($_templateOpt);
            switch ($product->getTypeId()) {
                case 'giftcard':
                    $child = $this->getLayout()->createBlock('enterprise_giftcard/catalog_product_view_type_giftcard_form', 'product.info.giftcard.form'
                    )->setTemplate('giftcard/catalog/product/view/type/giftcard/form.phtml');
                    $block->setChild('product.info.giftcard.form', $child);
                    break;
            }
            return $block->toHtml();
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customOptionsHtml(){
        return $this->getLayout()->createBlock('ampromo/items_options', '',
            array('product' => $this->getProduct()))->toHtml();
    }

    /**
     * @param Product $product
     * @param $width
     * @param $height
     * @return string
     */
    public function getImageUrl(Product $product, $width, $height){
        $helper = $this->_productImageHelper;
        $image = $helper->init($product, 'small_image')->resize($width, $height);
        return (string)$image;
    }
}
