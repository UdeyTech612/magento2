<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\MakeupCounter\Model\Entity\Attribute\Backend\Serialized;

use Exception;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Backend\Serialized;

/**
 * Class Json
 * @package Udeytech\MakeupCounter\Model\Entity\Attribute\Backend\Serialized
 */
class Json extends AbstractBackend
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * Index constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $json
    )
    {
        $this->_json = $json;
    }

    /**
     * Serialize before saving
     *
     * @param Object $object
     * @return Serialized
     */
    public function beforeSave($object)
    {
        // parent::beforeSave() is not called intentionally
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            $object->setData($attrCode, $this->_jsonEncodeIfRequired($object->getData($attrCode)));
        }
        return $this;
    }

    /**
     * Returns the $attributeValue in a JSON encoded form.  If the supplied value
     * is a string, we assume it's already been JSON encoded and avoid encoding
     * it a second time.
     * @param $attributeValue mixed Attribute value
     * @return string JSON encoded string
     */
    protected function _jsonEncodeIfRequired($attributeValue)
    {
        if (is_string($attributeValue)) {
            $attributeValue = $this->_json->unserialize($attributeValue);
        }
        if (is_array($attributeValue) && array_key_exists('__empty', $attributeValue)) {
            unset($attributeValue['__empty']);
        }
        return $this->_json->serialize($attributeValue);
    }

    /**
     * Try to unserialize the attribute value
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Serialized
     */
    protected function _unserialize(Varien_Object $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->getData($attrCode)) {
            try {
                $unserialized = $this->_json->unserialize($object->getData($attrCode));
                $object->setData($attrCode, $unserialized);
            } catch (Exception $e) {
                $object->unsetData($attrCode);
            }
        }
        return $this;
    }
}

?>
