<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Zend_Validate_Exception;

/**
 * Class UpgradeData
 * @package Udeytech\Instagram\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();
        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.2.0') < 0
        ) {
            /* Category instagram hashtag */
            /* @var CategorySetup $categorySetup */
            $categorySetupManager = $this->categorySetupFactory->create();
            $attr = $categorySetupManager->getAttribute(
                Category::ENTITY,
                'instagram_hashtag');
            if (empty($attr)) {
                $categorySetupManager->addAttribute(
                    Category::ENTITY,
                    'instagram_hashtag',
                    [
                        'group' => 'General Information',
                        'type' => 'text',
                        'label' => 'Instagram Hashtag: #',
                        'required' => false,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'user_defined' => true,
                        'default' => '',
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'visible' => 1
                    ]
                );
            }
        }
    }


}
