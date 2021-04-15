<?php
     /**
       * Copyright (c) 2021. Udeytech Technologies All rights reserved.
       * See COPYING.txt for license details.
       */
        declare(strict_types=1);
        namespace Udeytech\Productkit\Controller\Mineralmakeup;
        use Magento\Framework\App\Action\Action;
        use Magento\Framework\App\Action\Context;
        use Magento\Framework\Registry;
        use Magento\Framework\View\Result\PageFactory;
        use Udeytech\FreeSamplePage\Helper\Data;
        /**
         * Class Free
         * @package Udeytech\Productkit\Controller\Mineralmakeup
         */
class Addtocart extends Action {
        /**
        * @var Data
        */
        protected $_freeSampleHelper;
        /**
        * @var PageFactory
        */
        protected $_resultPageFactory;
        /**
        * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
        */
        protected $_productCollectionFactory;
        /**
        * @var Registrytions
        */
        protected $_registry;
        /**
        * @var \Magento\Core\Helper\Data
        */
        protected $_helper;
        /**
        * Addtocart constructor.
        * @param Context $context
        * @param PageFactory $resultPageFactory
        * @param Registry $registry
        * @param \Magento\Core\Helper\Data $helper
        * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
        * @param Data $freeSampleHelper
        */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Data $freeSampleHelper ){
        $this->_resultPageFactory = $resultPageFactory;
        $this->_freeSampleHelper = $freeSampleHelper;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }
    /**
    * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
    */
    public function execute() {
    $type = $this->getRequest()->getParam('type', '');
    $kitChooseId = $this->getRequest()->getParam('kit_choose_id', false);
    if (empty($type)) {
    $this->_redirect('*/*/custom');
    return $this;
    }
    $kitType = $this->helper('Udeytech\Productkit\Helper\Data')->getKitType($type);
    if(!$kitType) {
    $this->_redirect('*/*/custom');
    return $this;
    }
    try {
    $this->_init($type, $kitType);
    $kitProduct = $this->_productCollectionFactory->create()->load($this->_registry->registry('product')->getId());
    if($kitChooseId)
    $kitProduct->setProductkitChooseId($kitChooseId);
    $cart = $this->_getCart();
    $options = $kitProduct->getOptions();
    $_optionFound = false;
    foreach($options as $_option){
    if('products' == $_option->getTitle()){
    $_optionFound = true;
    break;
    }
    }
    if(!$_optionFound){
    throw new \Exception(__('Product Kit configuration error: custom option "products" not found'));
    }
    $params = $this->getRequest()->getParams();
    $params['options'] = array();
    if (!is_array($params['selected'])) {
    $params['selected'] = json_decode($params['selected']);
    }
    $params['selected'] = array_filter($params['selected']);
    if (\Udeytech\Productkit\Model\Source\Kit\Type::KIT_TYPE_FREE == $kitProduct->getData('productkit_kit_type')) {
    $params['selected'] = array_unique($params['selected']);
    }
    sort($params['selected']);
    $_selectedProduct = $this->_productCollectionFactory->create();
    foreach ($params['selected'] as $_selectedProductId) {
    $_selectedProduct->reset()->load($_selectedProductId);
    if (!$_selectedProduct->getId()){
    throw new \Exception(__('Invalid selected product ID %d', $_selectedProductId));
    }
    $_invalidProduct = false;
    switch ($_selectedProduct->getProductkitType()) {
    case \Udeytech\Productkit\Model\Source\Type::KIT_TYPE_CUSTOM:
        if(\Udeytech\Productkit\Model\Source\Type::KIT_TYPE_CUSTOM != $kitType){
            #$_invalidProduct = true;
        }
        break;
    case \Udeytech\Productkit\Model\Source\Type::KIT_TYPE_FREE:
        if (\Udeytech\Productkit\Model\Source\Type::KIT_TYPE_FREE != $kitType) {
            $_invalidProduct = true;
        }
        break;
    default:
        $_invalidProduct = true;
    }
    if($_invalidProduct){
    throw new \Exception(__('%s can not be selected for this Kit type', $_selectedProduct->getName()));
    }
    }
    $params['options'][$_option->getId()] = serialize($params['selected']);
    unset($params['selected']);
    if($this->_registry->registry('kit_quote_item')) {
    $cart->updateItem($this->_registry->registry('kit_quote_item')->getId(), new \Magento\Framework\DataObject($params));
    $message = __('%s was updated in your shopping cart.', $this->_helper->escapeHtml($this->_registry->registry('kit_quote_item')->getProduct()->getName()));
    $this->_getSession()->addSuccess($message);
    } else {
    $related = $this->getRequest()->getParam('related_product', false);
    $cart->addProduct($kitProduct, $params);
    if($related){
    $cart->addProductsByIds(explode(',', $related));
    }
    }
    $cart->save();
    }
    catch (\Exception $e) {
    $this->_getSession()->addError($e->getMessage());
    $this->_redirect('*/*/'.$type);
    return;
    }
    $this->_setRedirectToResponse('checkout/cart');
    }
    /**
    * @param $type
    * @param $kitType
    * @return $this
    * @throws \Exception
    */
    protected function _init($type, $kitType){
    if(!$this->_registry->registry('kit_quote_item')){
    $product = $this->helper('Udeytech\Productkit\Helper\Data')->getKitByName($type);
    if(!$product OR !$product->getId()) {
    throw new \Exception(__('Unable to process the Product Kit makeup'));
    }
    $this->_registry->register('product', $product);
    } else {
    $product = $this->_productCollectionFactory->create();
    $product->load($this->_registry->registry('kit_quote_item')->getProduct()->getId());
    if(!$product OR !$product->getId()){
    throw new \Exception(__('Unable to process the Product Kit makeup'));
    }
    $this->_registry->register('product', $product);
    }
    $this->_title($product->getName());
    return $this;
    }
    /**
    * @param $path
    */
    protected function _setRedirectToResponse($path){
    $url = $this->getUrl($path);
    $response['redirect'] = $url;
    $response = $this->_helper->jsonEncode($response);
    $this->getResponse()->setBody($response);
    }
    /**
    * @return \Magento\Checkout\Model\Session
    */
    protected function _getSession(){
    return $this->_checkoutSession;
    }
    /**
    * @return \Magento\Checkout\Model\Session
    */
    protected function _getCart()
    {
    return $this->_checkoutSession;
    }
}
?>
