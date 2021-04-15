<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Udeytech\PromoBannersLite\Model\ResourceModel;
/**
 * Class Banners
 * @package Udeytech\PromoBannersLite\Model\ResourceModel
 */
class Banners extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('udeytech_promobannerslite_banners', 'banners_id');
    }
}

