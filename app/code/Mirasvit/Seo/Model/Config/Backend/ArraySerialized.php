<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized as SerializedArraySerialized;
use Mirasvit\Core\Service\SerializeService;

class ArraySerialized extends SerializedArraySerialized
{
    /**
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $this->setValue(empty($value) ? false : $this->getUnserializedValue($value));
        }
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function getUnserializedValue($value)
    {
        if ($value != '[]' && json_decode($value)) { //M2.2 compatibility
            $decodedValue = SerializeService::decode($value);
            if (!$decodedValue) {
                $decodedValue = [0=>$value];
            }
            $value = $decodedValue;
        } elseif ($value != '[]') {
            $decodedValue = SerializeService::decode($value);
            if (!$decodedValue) {
                $decodedValue = [0=>$value];
            }
            $value = $decodedValue;
        }

        return $value;
    }

    /**
     * @return SerializedArraySerialized
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            unset($value['__empty']);
        }

        if ($this->getField() == 'noindex_pages2') {
            $value = $this->normalizeValue($value);
        }

        $this->setValue($value);

        return parent::beforeSave();
    }

    /**
     * @param string $value
     * @return mixed
     */
    private function normalizeValue($value)
    {
        $sortAlphabet = function ($elem1, $elem2) {
            return $elem1['pattern'] > $elem2['pattern'];
        };

        $sortCondition = function ($elem1, $elem2) {
            return strlen($elem2['pattern']) > strlen($elem1['pattern']) &&
                strrpos(str_replace(['/', '*'], [' ', ''], $elem2['pattern']), str_replace(['/', '*'], [' ', ''], $elem1['pattern'])) !== false;
        };

        if (is_array($value)) {
            uasort($value, $sortAlphabet);
            uasort($value, $sortCondition);
        }

        return $value;
    }
}
