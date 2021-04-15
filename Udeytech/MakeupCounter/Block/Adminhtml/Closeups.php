<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Udeytech\MakeupCounter\Block\Adminhtml;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class Closeups
 * @package Udeytech\MakeupCounter\Block\Adminhtml
 */
class Closeups extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Constructor
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_registry = $registry;
    }

    /**
     * Get options for select field Associated Products
     * @return string HTML
     */

    public function getAssociatedProductsOptionsHtml()
    {
        $associatedProducts = $this->getAssociatedProducts();
        $option = '';
        foreach ($associatedProducts as $product) {
            $option .= '<option value="' . $product->getId() . '" ';
            if ($this->_isProductInCurrentCloseup($product->getId())) {
                $option .= 'selected';
            }
            $option .= '>' . $product->getName() . '</option>';
        }
        return $option;
    }

    /**
     * Get associated products array
     * @return array
     */
    public function getAssociatedProducts()
    {
        $result = $this->getProduct()->getTypeInstance(true)->getAssociatedProducts($this->getProduct());
        return $result;
    }

    /**
     * Return catalog product object
     * @return Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Check is associated product bind with closeup
     * @return bool
     */

    protected function _isProductInCurrentCloseup($productId)
    {
        $closeup = $this->getCloseup();
        if (!isset($closeup['associated_products'])) {
            return false;
        }
        return in_array($productId, $closeup['associated_products']);
    }

    /**
     * Get closeup attribute value
     * @return array
     */
    public function getCloseup()
    {
        $closeups = $this->getProduct()->getData(Data::LOOKBOOK_CLOSEUPS_ATTR);
        if (isset($closeups[$this->getCloseupId()])) {
            return $closeups[$this->getCloseupId()];
        }
        return $this->_initCloseup();
    }

    /**
     * @return string
     */

    public function getCloseupId()
    {
        return $this->_registry->registry('current_closeup_id');
    }

    /**
     * Initialize empty closeup attribute
     * @return array
     */
    protected function _initCloseup()
    {
        $closeup = array('id' => $this->getCloseupId(), 'day' => 0, 'night' => 0, 'associated_products' => array());
        return $closeup;
    }

    /**
     * Get associated products count
     * @return int
     */
    public function getAssociatedProductsCount()
    {
        return count($this->getAssociatedProducts());
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/closeups/save');
    }
}

