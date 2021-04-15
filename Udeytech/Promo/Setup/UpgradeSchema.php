<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Udeytech\Promo\Setup;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Udeytech\Promo\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface {
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
            $connection = $setup->getConnection();
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_enable',
            ['type' => Table::TYPE_SMALLINT,
                'length' => 6,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Enable'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_enable',
            ['type' => Table::TYPE_SMALLINT,
                'length' => 6,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Enable'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_label_enable',
            ['type' => Table::TYPE_SMALLINT,
                'length' => 6,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Label Enable'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_prefix',
            ['type' => Table::TYPE_TEXT,
                'length' => 124,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Prefix'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_description',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Description'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_img',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Image'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_alt',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Alt'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_hover_text',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Text'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_link',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Link'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_top_banner_gift_images',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Top Banner Gift Image'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_description',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Description'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_img',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Image'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_alt',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Alt'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_hover_text',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'After Name Banner Hover Text'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_link',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Link'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_after_name_banner_gift_images',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo After Name Banner Gift Images'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_label_img',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Label Image'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_label_alt',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Label Alt'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_discount_value',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Discount Value'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_min_price',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Min Price'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_use_discount_amount',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Use Discount Amount'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_show_orig_price',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Show Original Price'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_free_shipping',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Free Shipping'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'amstore_ids',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Store Id'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'ampromo_type',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Type'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'promo_sku',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Promo Sku'
            ]);
            $connection->addColumn($setup->getTable('salesrule/rule'),
            'discount_step',
            ['type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Discount Step'
            ]);
        }
}
?>
