<?php
/**
 * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Udeytech\MakeupCounter\Model\Entity\Attribute\Backend\Serialized;

use Exception;
use InvalidArgumentException;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Backend\Serialized;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Backend model for attribute that stores structures in json format
 * @api
 * @since 101.0.0
 */
class JsonEncodedCus extends AbstractBackend
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * ArrayBackend constructor.
     * @param Json $jsonSerializer
     */
    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Serialize before saving
     * @param Object $object
     * @return Serialized
     */
    public function beforeSave($object)
    {
        // parent::beforeSave() is not called intentionally
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode) && !$this->isJsonEncoded($object->getData($attrCode))) {
            $object->setData($attrCode, $this->jsonSerializer->serialize($object->getData($attrCode)));
        }
        return $this;
    }

    /**
     * @param $value
     * @return bool
     */
    private function isJsonEncoded($value): bool
    {
        $result = is_string($value);
        if ($result) {
            try {
                $this->jsonSerializer->unserialize($value);
            } catch (InvalidArgumentException $e) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Try to unserialize the attribute value
     * @param DataObjectFactory $object
     * @return Serialized
     */
    protected function _unserialize(DataObjectFactory $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->getData($attrCode)) {
            try {
                $unserialized = $this->jsonSerializer->unserialize($object->getData($attrCode));
                $object->setData($attrCode, $unserialized);
            } catch (Exception $e) {
                $object->unsetData($attrCode);
            }
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
        return $this->jsonSerializer->serialize($attributeValue);
    }
}

?>
