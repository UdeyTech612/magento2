<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PrePromoQuoteIndex
 * @package Udeytech\Promo\Model\Observer
 */
class PrePromoQuoteIndex implements ObserverInterface {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var array
     */
    protected $_arrayWithSimpleAction = array(
        'buy_x_get_y_percent',
        'buy_x_get_y_fixdisc',
        'buy_x_get_y_fixed',
        'buy_x_get_n_percent',
        'buy_x_get_n_fixdisc',
        'buy_x_get_n_fixed',
        'setof_percent',
        'setof_fixed',
        'ampromo_items',
        'ampromo_cart',
        'ampromo_spent',
        'ampromo_product');
    /**
     * @var array
     */
    protected $_arrayWithProductSet = array('setof_percent', 'setof_fixed');

    /**
     * PrePromoQuoteIndex constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_scopeConfig->getvalue('ampromo/messages/show_stock_warning')) {
            $resultArray          = array();
            $actionsAndConditions = array();
            //   //->('salesrule/rule')
            $rulesCollection = $this->_productSaleRule->create()
                ->getCollection()->addFieldToSelect('actions_serialized')
                ->addFieldToSelect('conditions_serialized')->addFieldToSelect('promo_sku')
                ->addFieldToSelect('simple_action')->addFieldtoFilter('is_active', 1);
            $rulesData = $rulesCollection->getData();
            $arrayWithSku = array();
            foreach ($rulesData as $rule) {
                if (isset($rule['promo_sku']) && in_array($rule['simple_action'], $this->_arrayWithSimpleAction)
                ) {
                    $arrayWithSku = array_merge($arrayWithSku, $this->_convertAndFormat($rule['promo_sku']));
                }
                if (!in_array($rule['simple_action'], $this->_arrayWithProductSet)) {
                    $actionsAndConditions[] = unserialize($rule['actions_serialized']);
                    $actionsAndConditions[] = unserialize($rule['conditions_serialized']);
                }
            }
            $skuArray = $this->_checkActionForSku($actionsAndConditions, $arrayWithSku);
            if (!empty($skuArray[self::EQUALS])
                || !empty($skuArray[self::CONTAINS])
            ) {
                $resultArray = $this->_checkForSku($skuArray);
            }

            if ($resultArray) {
                $this->_generateMessage($resultArray);
            }
        }
    }

    /**
     * @param $sku
     * @return array
     */
    protected function _convertAndFormat($sku)
    {
        $skusFromRules = explode(',', $sku);
        $skusFromRules = array_map('trim', $skusFromRules);

        return $skusFromRules;
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
     * @param $conditions
     * @param $searchFor
     * @return array|null
     */
    protected function _recGetArrayWithSkus($conditions, $searchFor){
        static $arrayWithSku = array(array(), array());
        static $arrayWithEqualSkus = array();
        foreach ($conditions as $key => $condition) {
            if ($key == $searchFor && is_string($condition) && $condition != ""){
                if ($conditions['attribute'] == 'sku' || $conditions['attribute'] == 'quote_item_sku'){
                    if ($conditions['operator'] == "{}" || $conditions['operator'] == "!{}"){
                        $arrayWithEqualSkus[] = $condition;
                    } else {
                        $arrayWithSku[self::EQUALS][] = $condition;
                    }
                }
            }
            if (is_array($conditions[$key])) {
                $this->_recGetArrayWithSkus($condition, $searchFor);
            }
        }
        if (!empty($arrayWithEqualSkus)) {
            $arrayWithSku[self::CONTAINS] = array_unique($arrayWithEqualSkus);
        }
        if (!empty($arrayWithSku[self::EQUALS]) || !empty($arrayWithSku[self::CONTAINS])){
            return $arrayWithSku;
        }
        return null;
    }
}
