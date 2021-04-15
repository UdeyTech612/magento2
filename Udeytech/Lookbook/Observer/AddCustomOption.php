<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Lookbook\Observer;

use Exception;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Udeytech\Lookbook\Helper\Data;
use Udeytech\Lookbook\Model\PositionsFactory;
use Udeytech\Lookbook\Model\ResourceModel\Positions\CollectionFactory;

/**
 * Class AddCustomOption
 * @package Udeytech\Lookbook\Observer
 */
class AddCustomOption implements ObserverInterface
{
    /**
     *
     */
    const POS_MULT = 100000000;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var Image
     */
    protected $_imageHelper;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var ProductFactory
     */
    protected $_productloader;
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var RequestInterface
     */
    protected $_request;
    /**
     * @var PositionsFactory
     */
    protected $_positionsRepository;

    /**
     * AddCustomOption constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Image $imageHelper
     * @param Data $helper
     * @param ProductFactory $_productloader
     * @param RequestInterface $request
     * @param CollectionFactory $collectionFactory
     * @param PositionsFactory $positionsRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Image $imageHelper,
        Data $helper,
        ProductFactory $_productloader,
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        PositionsFactory $positionsRepository
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_positionsRepository = $positionsRepository;
        $this->_imageHelper = $imageHelper;
        $this->_productloader = $_productloader;
        $this->_collectionFactory = $collectionFactory;
        $this->_helper = $helper;
        $this->_request = $request;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $params = $this->_request->getParam('associated_products');
        $paramsa = $this->_request->getParam('associated_products1');
        if ($params) {
            foreach ($params as $key => $param) {
                $data = $param;
                $data['parent_id'] = $this->_request->getParam('id');
                $data['simple_id'] = $key;
                $item = $this->_collectionFactory->create()->load()->addFieldToFilter('parent_id', $data['parent_id'])
                    ->addFieldToFilter('simple_id', $key)->getLastItem();
                //if ($id = $item->getId()){
                //   $data['id'] = $id;
                // }
                $data['pos_x'] = $data['pos_x'] * self::POS_MULT;
                $data['pos_y'] = $data['pos_y'] * self::POS_MULT;
                $data['type'] = $this->_request->getParam('looktype');
                $datasave = $this->_positionsRepository->create();
                $datasave->setData($data);
                $datasave->save();
            }

        }
        if ($paramsa) {
            foreach ($paramsa as $key => $param) {
                $data = $param;
                $data['parent_id'] = $this->_request->getParam('id');
                $data['simple_id'] = $key;
                $item = $this->_collectionFactory->create()->load()
                    ->addFieldToFilter('parent_id', $data['parent_id'])
                    ->addFieldToFilter('simple_id', $key)->getLastItem();
                //if ($id = $item->getId()){
                //$data['id'] = $id;
                //}
                $data['pos_x'] = $data['pos_x'] * self::POS_MULT;
                $data['pos_y'] = $data['pos_y'] * self::POS_MULT;
                $data['type'] = $this->_request->getParam('looktype1');
                $datasave = $this->_positionsRepository->create();
                $datasave->setData($data);
                $datasave->save();
            }
        }
    }

    /**
     * @param Observer $observer
     */
    public function defineImagePosition(Observer $observer)
    {
        $request = $observer->getEvent()->getAction()->getRequest();
        if ($request->getModuleName() == 'catalog'
            && $request->getControllerName() == 'product' && $this->_helper->isFrontendEnabled()) {
            $product = $this->_productloader->create()->load($request->getParam('id'));
            try {
                if ((string)$this->_imageHelper->init($product, 'udeytech_lookbook_image')) {
                    $imagePosition = $this->getImagePosition($product);
                    if ($imagePosition == 'top') {
                        $handle = 'lookbook_top';
                    } else {
                        $handle = 'lookbook_left';
                    }
                    $layoutUpdate = $observer->getEvent()->getLayout()->getUpdate();
                    $layoutUpdate->addHandle($handle);
                    $layoutUpdate->addHandle('lookbook_main');
                }
            } catch (Exception $e) {
                /* there is no udeytech_lookbook_image' for this product */
            }
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getImagePosition($product)
    {
        if ($position = $product->getUdeytechLookbookImagepos()) {
            return $position;
        }
        return $this->_scopeConfig->getValue('lookbook/settings/img_position', ScopeInterface::SCOPE_STORE);
    }
}
