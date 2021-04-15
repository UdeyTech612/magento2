<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\Instagram\Block\Html;

use Magento\Framework\View\Element\Template;

/**
 * Class Head
 * @package Udeytech\Instagram\Block\Html
 */
class Head extends Template
{
    /**
     * @var string
     */
    protected $_template = 'foot.phtml';

    /**
     *
     * @var array
     */
    protected $_items = [];

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     *
     * @param string $type
     * @param string $path
     * @param string $params
     */
    public function addItem($type, $name, $params = null, $if = null, $cond = null)
    {
        if ($type === 'skin_css' && empty($params)) {
            $params = 'media="all"';
        }
        $this->_items[$type . '/' . $name] = array(
            'type' => $type,
            'name' => $name,
            'params' => $params,
            'if' => $if,
            'cond' => $cond,
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getCssJsHtml()
    {
        $lines = [];
        foreach ($this->_items as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
            }
        }

        // prepare HTML
        $head = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // open !IE conditional using raw value
                if (strpos($if, "><!-->") !== false) {
                    $head .= $if . "\n";
                } else {
                    $head .= '<!--[if ' . $if . ']>' . "\n";
                }
            }

            // static and skin css
            $head .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />' . "\n",
                empty($items['js_css']) ? array() : $items['js_css'],
                empty($items['skin_css']) ? array() : $items['skin_css'],
                null
            );

            // static and skin javascripts
            $head .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                empty($items['js']) ? array() : $items['js'],
                empty($items['skin_js']) ? array() : $items['skin_js'],
                null
            );

            if (!empty($if)) {
                // close !IE conditional comments correctly
                if (strpos($if, "><!-->") !== false) {
                    $head .= '<!--<![endif]-->' . "\n";
                } else {
                    $head .= '<![endif]-->' . "\n";
                }
            }
        }
        return $head;
    }

    /**
     * Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
     *
     * Will attempt to merge into 1 directive, if merging callback is provided. In this case it will generate
     * filenames, rather than render urls.
     * The merger callback is responsible for checking whether files exist, merging them and giving result URL
     *
     * @param string $format - HTML element format for sprintf('<element src="%s"%s />', $src, $params)
     * @param array $staticItems - array of relative names of static items to be grabbed from js/ folder
     * @param array $skinItems - array of relative names of skin items to be found in skins according to design config
     * @param callback $mergeCallback
     * @return string
     */
    protected function _prepareStaticAndSkinElements(
        $format, array $staticItems, array $skinItems
    )
    {
        $items = array();

        // get static files from the js folder, no need in lookups
        foreach ($staticItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $this->getViewFileUrl($name);
            }
        }

        // lookup each file basing on current theme configuration
        foreach ($skinItems as $params => $rows) {
            foreach ($rows as $name) {
                $items[$params][] = $this->getViewFileUrl($name);
            }
        }

        $html = '';
        foreach ($items as $params => $rows) {
            // render elements
            $params = trim($params);
            $params = $params ? ' ' . $params : '';
            foreach ($rows as $src) {
                $html .= sprintf($format, $src, $params);
            }
        }
        return $html;
    }
}
