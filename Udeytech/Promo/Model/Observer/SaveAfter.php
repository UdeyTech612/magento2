<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveAfter
 * @package Udeytech\Promo\Model\Observer
 */
class SaveAfter implements ObserverInterface {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * SaveAfter constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        LoggerInterface $logger,
        //\Magento\Core\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request){
        $this->logger = $logger;
        //$this->_helper = $helper;
        $this->request = $request;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $rule = $observer->getRule();
        $conditions = $rule->getConditions()->asArray();
//        if(!$this->_helper->isModuleEnabled('Udeytech_Rules')) {
//            $unsafeIs = 0;
//            if(isset($conditions['conditions'])) {
//                $unsafeIs = $this->checkForIs($conditions['conditions']);
//            }
//            $actions = $rule->getActions()->asArray();
//            if(isset($actions['conditions']) && !$unsafeIs) {
//                $unsafeIs = $this->checkForIs($actions['conditions']);
//            }
//            if($unsafeIs) {
//                $this->messageManager->addNotice('It is more safe to use `is one of` operator and not `is` for comparison.  Please correct if the rule does not work as expected.');
//               }
//            }
        if($this->_scopeConfig->getValue('ampromo/messages/show_stock_warning')) {
            $skuArray = array();
            $checkActionForSku = array(array(), array());
            $promoSku = $rule->getPromoSku();
            $trimPromoSku = trim($promoSku);
            if (!empty($trimPromoSku) && in_array($rule->getSimpleAction(), $this->_arrayWithSimpleAction)){
                $skuArray = $this->_checkActionForPromoSku($rule);
            }
            $actions[] = $conditions;
            $checkActionForSku = $this->_checkActionForSku($actions);
            if($skuArray){
                $checkActionForSku[self::EQUALS] = array_merge($checkActionForSku[self::EQUALS], $skuArray);
            }
            if (!empty($checkActionForSku[self::EQUALS])
                || !empty($checkActionForSku[self::CONTAINS])
            ) {
                $skuArray = $this->_checkForSku($checkActionForSku);
            }
            $getParamBack = $this->request->getRequest()->getParam('back');
            if ($skuArray && $rule->getIsActive() && $getParamBack) {
                $this->_generateMessage($skuArray);
            }
        }
    }

    /**
     * @param $outOfStockSkus
     */
    protected function _generateMessage($outOfStockSkus){
        $arrayWithLinks = array();
        if($outOfStockSkus){
            foreach ($outOfStockSkus as $outOfStockSku) {
                $url = $this->getUrl('/catalog_product/edit',
                    array('id' => $outOfStockSku['entity_id']));
                $arrayWithLinks[] = '<a href="' . $url . '"target="_blank">' . $outOfStockSku['sku'] . '</a>';
            }
            $strWithLinks = implode($arrayWithLinks, ', ');
            if($strWithLinks != ""){
                $message = __("Please notice, the %s have stock quantity <= 0 or are \"Out of stock\". That may interfere in proper rule execution.",
                    $strWithLinks);
                $this->_messageManager->createMessage()->addWarning($message);
            }
        }
    }

    /**
     * @param $actionsAndConditions
     * @param null $promoSkus
     * @return array
     */
    protected function _checkActionForSku($actionsAndConditions, $promoSkus = null)
    {
        $skus = $this->_recGetArrayWithSkus($actionsAndConditions, 'value');
        $arrayWithOutOfStock = array(array(), array());
        /*
         * from recursive we get array(arrayWithOthers(), arrayWithLikes())
         * */
        if (!empty($skus)) {
            $count = count($skus);
            for ($i = 0; $i < $count; $i ++) {
                $skus[$i] = array_unique($skus[$i]);
                foreach ($skus[$i] as $sku) {
                    /*
                     * из рекурсии иногда может прийти "лишние" элементы-массивы
                     * эта проверка необходима для отсеивания их
                     * */
                    if (!is_array($sku)) {
                        $arrayWithOutOfStock[$i] = array_merge(
                            $arrayWithOutOfStock[$i],
                            $this->_convertAndFormat($sku)
                        );
                    }
                }
            }
        }
        if ($promoSkus) {
            $arrayWithOutOfStock[self::EQUALS] = array_merge($arrayWithOutOfStock[self::EQUALS], $promoSkus);
        }

        return $arrayWithOutOfStock;
    }

    /**
     * @param $rule
     * @return array
     */
    protected function _checkActionForPromoSku($rule){
        $strWithSku    = $rule->getPromoSku();
        $actions       = $rule->getActions()->getData('actions');
        $outOfStockSku = array();
        foreach ($actions as $action) {
            if ($action['attribute'] == "quote_item_sku") {
                $strWithItemsSku = $action['value'];
                $outOfStockSku   = $this->_convertAndFormat($strWithItemsSku);
            }
        }
        if ($strWithSku != "") {
            $arrayWithSku  = $this->_convertAndFormat($strWithSku);
            $outOfStockSku = array_merge($outOfStockSku, $arrayWithSku);
        }
        return array_unique($outOfStockSku);
    }

    /**
     * @param $sku
     * @return array
     */
    protected function _convertAndFormat($sku){
        $skusFromRules = explode(',', $sku);
        $skusFromRules = array_map('trim', $skusFromRules);
        return $skusFromRules;
    }
}
