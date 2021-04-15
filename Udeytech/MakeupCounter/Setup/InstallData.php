<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Creating Product attribute
 */

namespace Udeytech\MakeupCounter\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Test\Demo\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $entity_type = Product::ENTITY;
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute($entity_type,
            'udeytech_lookbook_close_ups',
            [
                'type' => 'text',
                'label' => 'Lookbook Close-ups',
                'input' => 'hidden',
                'backend' => 'Udeytech\MakeupCounter\Model\Entity\Attribute\Backend\Serialized\JsonEncodedCus',
                'sort_order' => 4,
                'visible' => false,
                'required' => FALSE,
                'user_defined' => true,
                'default' => '',
                "apply_to" => "grouped",
                'global' => ScopedAttributeInterface::SCOPE_STORE
            ]
        );
    }
}
