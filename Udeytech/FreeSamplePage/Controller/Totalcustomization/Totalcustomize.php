<?php

/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\FreeSamplePage\Controller\Totalcustomization;
use Exception;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Totalcustomization
 * @package Udeytech\FreeSamplePage\Controller\Totalcustomization
 */

class Totalcustomize extends Action
{
    /**
     *
     */
    const BUNDLE_SELECTION_TABLE = "catalog_product_bundle_selection";
    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * Totalcustomization constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
     * @param ResourceConnection $resourceConnection
     * @param Cart $cart
     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductFactory $productFactory,
        ResourceConnection $resourceConnection,
        Cart $cart){
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */

    public function execute(){
         $post = $this->getRequest()->getPostValue();
        $params = [
          'product' => 2806,
          'related_product' => null,
          'bundle_option' => [
               13 => 3372,
               20 => 3372,
               14 => 3995,
               16 => 3324,
        ],'options' => [5 => 'Some Test value to a text field',],'qty' => 1];
$product = $this->productFactory->getById(2806);
$this->cart->addProduct($product, $params);
$this->cart->save();
    echo "testnewdaata";
         $dataarray = array("3372","3372","3995","4288","3324");
           print_r($dataarray); die('testnewdatasss');
        try {
            $myJSON = preg_replace('/[^A-Za-z0-9,. -]/', '', $post['selected']);
            $posts = explode(',', $myJSON);
            $bundleId = $post['productId'];
            if ($bundleId) {
                $bundleId = $post['productId'];
            } else {
                $bundleId = 2806;
            }
            if ($post['qty']) {
                $qtyt = $post['qty'];
            } else {
                $qtyt = 1;
            }
            foreach ($dataarray as $prductId) {
                $optionidd = $this->getOptionId($bundleId, $productId);
                $selectionId[$optionidd['option_id']] = $this->getSelectionId($bundleId, $productId);
            }
            $product = $this->productFactory->create()->load($bundleId);
            $params = [
                'product' => $bundleId,
                'bundle_option' => $selectionId,
                'qty' => $qtyt
            ];
            if ($product->getId()) {
                $this->cart->addProduct($product, $params);
                $this->cart->save();
            }
            $this->messageManager->addSuccess(__('Add to cart successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addException($e, __('%1', $e->getMessage()));
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('error.'));
        }
    }
    /**
     * @param int $bundleItemId
     * @param int $childItemId
     * @return array
    public function getOptionId(int $bundleItemId, int $childItemId){
        $tableName = $this->resourceConnection->getTableName(self::BUNDLE_SELECTION_TABLE);
        $connection = $this->resourceConnection->getConnection();
        $query = "Select option_id FROM `" . $tableName . "` WHERE parent_product_id = $bundleItemId and product_id = $childItemId";
        $result = $connection->fetchRow($query);
        return $result;
    }
    /**
     * @param int $bundleItemId
     * @param int $childItemId
     * @return array
     */
    public function getSelectionId(int $bundleItemId, int $childItemId) {
        $tableName = $this->resourceConnection->getTableName(self::BUNDLE_SELECTION_TABLE);
        $connection = $this->resourceConnection->getConnection();
        $query = "Select selection_id FROM `" . $tableName . "` WHERE parent_product_id = $bundleItemId and product_id = $childItemId";
        $result = $connection->fetchAll($query);
        return $result;
    }
}
