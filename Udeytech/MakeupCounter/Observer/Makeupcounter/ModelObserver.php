<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Udeytech\MakeupCounter\Observer\Makeupcounter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template\Context;
use Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory;
use Udeytech\MakeupCounter\Helper\Data;

/**
 * Class ModelObserver
 * @package Udeytech\MakeupCounter\Observer\Makeupcounter
 */
class ModelObserver implements ObserverInterface
{
    /**
     *
     */
    const POS_MULT = 100000000;
    /**
     * Margin for reset positions in percent
     */
    const MARGIN_RESET_POSITIONS = 5;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var
     */
    protected $productCollectionFactory;
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * ModelObserver constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Data $helper,
        CollectionFactory $collectionFactory,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Execute observer
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ){
        //Your observer code
    }
    /**
     * Saving lookBook positions for current product
     */
    public function saveProduct(Magento\Framework\Event\Observer $observer)
    {
        $params = $this->getRequest()->getParam('associated_products');
        $parentId = $this->getRequest()->getParam('id');
        if (!$params || !$parentId)
            return false;
        foreach ($params as $type => $datas) {
            $this->_resetProductPosition($parentId, $datas);
            foreach ($datas as $key => $data) {
                $data['parent_id'] = $parentId;
                $data['simple_id'] = $key;
                $data['type'] = $type;
                $item = $this->_collectionFactory->getCollection()->addFieldToFilter('parent_id', $data['parent_id'])
                    ->addFieldToFilter('simple_id', $key)->addFieldToFilter('type', $type)->getLastItem();
                if ($id = $item->getId()) {
                    $data['id'] = $id;
                }
                $marginReset = self::MARGIN_RESET_POSITIONS / 100;
                if ($data['pos_x'] < $marginReset
                    || $data['pos_x'] > 1 - $marginReset
                    || $data['pos_y'] < $marginReset
                    || $data['pos_y'] > 1 - $marginReset) {
                    $data['pos_x'] = 0;
                    $data['pos_y'] = 0;
                } else {
                    $data['pos_x'] = $data['pos_x'] * self::POS_MULT; //pow(10,8);
                    $data['pos_y'] = $data['pos_y'] * self::POS_MULT; //pow(10,8);
                }
                $this->_collectionFactory->setData($data)->save();
            }
        }
    }

    /**
     * @param $parentId
     * @param $items
     */
    protected function _resetProductPosition($parentId, $items)
    {
        $simpleIds = array_keys($items);
        $collection = $this->_collectionFactory->getResourceCollection();
        $collection->addFieldToFilter('parent_id', $parentId)->addFieldToFilter('simple_id', array('nin' => $simpleIds));
        foreach ($collection as $item) {
            $item->delete();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function defineLookBook(Observer $observer)
    {
        $request = $observer->getEvent()->getAction()->getRequest();
        if ($request->getModuleName() == 'catalog'
            && $request->getControllerName() == 'product'
            && $this->_helper->isFrontendEnabled()) {
            $product = $this->productCollectionFactory->create()->load($request->getParam('id'));
            try {
                if ((int)$product->getAttributeSetId() == (int)$this->_helper->getAttributeSetId()) {
                    $layoutUpdate = $observer->getEvent()->getLayout()->getUpdate();
                    $layoutUpdate->addHandle('makeupcounter_main');
                }
            } catch (Exception $e) {
                /* there is no udeytech_lookbook_image' for this product */
            }
        }
    }
}

