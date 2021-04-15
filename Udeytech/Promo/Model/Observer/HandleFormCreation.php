<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Model\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * Class HandleFormCreation
 * @package Udeytech\Promo\Model\Observer
 */
class HandleFormCreation implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * HandleFormCreation constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
     public function execute(\Magento\Framework\Event\Observer $observer) {
        $actionsSelect = $observer->getForm()->getElement('simple_action');
        if ($actionsSelect) {
            $vals = $actionsSelect->getValues();
            $vals[] = array('value' => 'ampromo_items','label' => __('Auto add promo items with products'),);
            $vals[] = array('value' => 'ampromo_cart', 'label' => __('Auto add promo items for the whole cart'),);
            $vals[] = array('value' => 'ampromo_product', 'label' => __('Auto add the same product'),);
            $vals[] = array('value' => 'ampromo_spent','label' => __('Auto add promo items for every $X spent'),);
            $actionsSelect->setValues($vals);
            $actionsSelect->setOnchange('ampromo_hide_all();');
            $fldSet = $observer->getForm()->getElement('action_fieldset');
            $fldSet->addField('ampromo_type', 'select', array('name' => 'ampromo_type','label' => __('Type'),
                'values' => array(0 => __('All SKUs below'), 1 => __('One of the SKUs below')),
            ),
                'discount_amount'
            );
            $fldSet->addField('promo_sku', 'text', array(
                'name' => 'promo_sku',
                'label' => __('Promo Items'),
                'note' => __('Comma separated list of the SKUs'),
            ),
                'ampromo_type'
            );
        }
        return $this;
    }
}
