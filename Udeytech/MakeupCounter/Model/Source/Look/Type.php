<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\MakeupCounter\Model\Source\Look;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Type
 * @package Udeytech\MakeupCounter\Model\Source\Look
 */
class Type extends AbstractSource
{
    /**
     *
     */
    const LOOK_TYPE_NONE = '';
    /**
     *
     */
    const LOOK_TYPE_DAY_NIGHT = 1;
    /**
     *
     */
    const LOOK_TYPE_BEFORE_AFTER = 2;
    /**
     *
     */
    const LOOK_TYPE_DAY_NIGHT_TEXT = 'Day/night';
    /**
     *
     */
    const LOOK_TYPE_BEFORE_AFTER_TEXT = 'Before/after';
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }
        return $result;
    }
    /**
     * @return array
     */
    protected function _getValues()
    {
        return array(
            self::LOOK_TYPE_NONE => __('None'),
            self::LOOK_TYPE_DAY_NIGHT => __('Day/night'),
            self::LOOK_TYPE_BEFORE_AFTER => __('Before/after'),
        );
    }
    /**
     * @param int|string $value
     * @return bool|mixed|string|null
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }
    /**
     * @param $type
     * @return int|string
     */
    public function getValueByType($type)
    {
        if (self::LOOK_TYPE_DAY_NIGHT_TEXT == $type) return self::LOOK_TYPE_DAY_NIGHT;
        else if (self::LOOK_TYPE_BEFORE_AFTER_TEXT == $type) return self::LOOK_TYPE_BEFORE_AFTER;
        else return self::LOOK_TYPE_NONE;
    }
    /**
     * @param $type
     * @return string
     */
    public function getTypeByValues($type){
        if (self::LOOK_TYPE_DAY_NIGHT == $type) return self::LOOK_TYPE_DAY_NIGHT_TEXT;
        else if (self::LOOK_TYPE_BEFORE_AFTER == $type) return self::LOOK_TYPE_BEFORE_AFTER_TEXT;
        else return self::LOOK_TYPE_NONE;
    }
}
