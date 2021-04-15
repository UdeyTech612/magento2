<?php
/**
 * Copyright © Udeytech @2020 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\Promo\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Udeytech\Promo\Model\Source\DisplayMode;

/**
 * Class Items
 * @package Udeytech\Promo\Block
 */
class Items extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Items constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getItemsByRule(){
        if (!$this->hasData('items_by_rule')) {
            $products = $this->getItems();
            $result = array();
            foreach ($products as $product) {
                $rule = $product->getData('ampromo_rule');
                if (!array_key_exists($rule->getId(), $result)) {
                    $result[$rule->getId()] = array('rule' => $rule, 'products' => array());
                }
                $result[$rule->getId()]['products'][] = $product;
            }
            $this->setData('items_by_rule', $result);
        }
        return $this->getData('items_by_rule');
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        if (!$this->hasData('items')) {
            $helper = $this->helper('Udeyetch\Promo\Helper\Data');
            $products = $helper->getNewItems(true);
            $this->setData('items', $products);
        }
        return $this->getData('items');
    }

    /**
     * @param Product $product
     * @param null $rule
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderItem(Product $product, $rule = null)
    {
        $block = $this->getLayout()->createBlock('ampromo/items_item', 'ampromo_item_' . $product->getId(),
            array('product' => $product, 'rule' => $rule));
        $block->setParentBlock($this);
        return $block->toHtml();
    }

    /**
     * @return string
     */
    public function getModeName()
    {
        $mode = $this->getBaseUrl('ampromo/popup/mode');
        return $mode == DisplayMode::MODE_INLINE ? 'inline' : 'popup';
    }

    /**
     * @return bool
     */
    public function useCarousel()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $pattern = $this->scopeConfig->getValue('ampromo/popup/mode', $storeScope);
        if ($pattern == DisplayMode::MODE_INLINE) {
            return false;
        }
        $items = $this->getItems();
        if (!$items || count($items) <= 2) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getPopupHeader()
    {
        return trim($this->scopeConfig->getValue('ampromo/popup/block_header'));
    }


    /**
     * @return string
     */
    public function getAddToCartText()
    {
        return trim($this->scopeConfig->getValue('ampromo/popup/add_to_cart_text'));
    }


    /**
     * @return mixed
     */
    public function getOptionsJs()
    {
        return $this->getJsLayout()->createBlock('core/template')->setTemplate('catalog/product/view/options/js.phtml')->toHtml();
    }

    /**
     * @return mixed
     */
    public function getCalendarJs()
    {
        return $this->getJsLayout()->createBlock('core/html_calendar')->setTemplate('page/js/calendar.phtml')->toHtml();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (count($this->getItems()) > 0) {
            if ($this->scopeConfig->isSetFlag('ampromo/popup/multiselect') && $this->scopeConfig->getValue('ampromo/popup/mode') == DisplayMode::MODE_INLINE) {
                $this->setTemplate('amasty/ampromo/items/by_rule.phtml');
            }
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        $params = $this->helper('Udeytech\Promo\Helper\Data')->getUrlParams();
        return $this->getUrl('ampromo/cart/updateMultiple', $params);
    }
}

