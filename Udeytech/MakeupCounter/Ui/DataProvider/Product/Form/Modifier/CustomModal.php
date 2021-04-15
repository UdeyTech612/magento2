<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\MakeupCounter\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;

/**
 * Class CustomModal
 * @package Udeytech\MakeupCounter\Ui\DataProvider\Product\Form\Modifier
 */
class CustomModal extends AbstractModifier
{
    /**
     *
     */
    const CUSTOM_MODAL_LINK = 'custom_modal_link';
    /**
     *
     */
    const CUSTOM_MODAL_INDEX = 'custom_modal';
    /**
     *
     */
    const CUSTOM_MODAL_CONTENT = 'content';
    /**
     *
     */
    const CUSTOM_MODAL_FIELDSET = 'fieldset';
    /**
     *
     */
    const CONTAINER_HEADER_NAME = 'header';
    /**
     *
     */
    const FIELD_NAME_1 = 'field1';
    /**
     *
     */
    const FIELD_NAME_2 = 'field2';
    /**
     *
     */
    const FIELD_NAME_3 = 'field3';
    /**
     * @var LocatorInterface
     */
    protected $locator;
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var array
     */
    protected $meta = [];

    /**
     * CustomModal constructor.
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(LocatorInterface $locator, ArrayManager $arrayManager, UrlInterface $urlBuilder)
    {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addCustomModal();
        $this->addCustomModalLink(10);
        return $this->meta;
    }

    /**
     *
     */
    protected function addCustomModal()
    {
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::CUSTOM_MODAL_INDEX => $this->getModalConfig(),
            ]
        );
    }

    /**
     * @return array
     */
    protected function getModalConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.product_form_data_source',
                        'ns' => static::FORM_NAME,
                        'options' => [
                            'title' => __('Modal Title'),
                            'buttons' => [
                                [
                                    'text' => __('Save'),
                                    'class' => 'action-primary', // additional class
                                    'actions' => [
                                        [
                                            'targetName' => 'index = product_form', // Element selector
                                            'actionName' => 'save', // Save parent form (product)
                                        ],
                                        'closeModal', // method name
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                static::CUSTOM_MODAL_CONTENT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'container',
                                'dataScope' => 'data.product', // save data in the product data
                                'externalProvider' => 'data.product_data_source',
                                'ns' => static::FORM_NAME,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'behaviourType' => 'edit',
                                'externalFilterMode' => true,
                                'currentProductId' => $this->locator->getProduct()->getId(),
                            ],
                        ],
                    ],
                    'children' => [
                        static::CUSTOM_MODAL_FIELDSET => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Fieldset'),
                                        'componentType' => Fieldset::NAME,
                                        'dataScope' => 'custom_data',
                                        'collapsible' => true,
                                        'sortOrder' => 10,
                                        'opened' => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                static::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),
                                static::FIELD_NAME_1 => $this->getFirstFieldConfig(20),
                                static::FIELD_NAME_2 => $this->getSecondFieldConfig(30),
                                static::FIELD_NAME_3 => $this->getThirdFieldConfig(40),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __('You can write any text here'),
                    ],
                ],
            ],
            'children' => [],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getFirstFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Example Text Field'),
                        'formElement' => Field::NAME,
                        'componentType' => Input::NAME,
                        'dataScope' => static::FIELD_NAME_1,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getSecondFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Options Select'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'dataScope' => static::FIELD_NAME_2,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->_getOptions(),
                        'visible' => true,
                        'disabled' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = [];
        $productOptions = $this->locator->getProduct()->getOptions() ?: [];
        /** @var Option $option */
        foreach ($productOptions as $index => $option) {
            $options[$index]['label'] = $option->getTitle();
            $options[$index]['value'] = $option->getId();
        }

        return $options;
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getThirdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Options Multiselect'),
                        'componentType' => Field::NAME,
                        'formElement' => MultiSelect::NAME,
                        'dataScope' => static::FIELD_NAME_3,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->_getOptions(),
                        'visible' => true,
                        'disabled' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     */
    protected function addCustomModalLink($sortOrder)
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                CustomOptions::GROUP_CUSTOM_OPTIONS_NAME => [
                    'children' => [
                        CustomOptions::CONTAINER_HEADER_NAME => [
                            'children' => [
                                static::CUSTOM_MODAL_LINK => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'title' => __('Open Custom Modal'),
                                                'formElement' => Container::NAME,
                                                'componentType' => Container::NAME,
                                                'component' => 'Magento_Ui/js/form/components/button',
                                                'actions' => [
                                                    [
                                                        'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                                            . static::CUSTOM_MODAL_INDEX, // selector
                                                        'actionName' => 'openModal', // method name
                                                    ],
                                                ],
                                                'displayAsLink' => false,
                                                'sortOrder' => $sortOrder,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}

?>
