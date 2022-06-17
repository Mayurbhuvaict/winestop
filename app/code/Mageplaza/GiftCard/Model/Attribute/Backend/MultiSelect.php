<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Model\Attribute\Backend;

use Magento\Framework\DataObject;

/**
 * Class MultiSelect
 * @package Mageplaza\GiftCard\Model\Attribute\Backend
 */
class MultiSelect extends AbstractClass
{
    /**
     * Before save method
     *
     * @param DataObject $object
     *
     * @return AbstractClass|$this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $data          = $object->getData($attributeCode);
        if (!is_array($data)) {
            $data = [];
        }

        $object->setData($attributeCode, implode(',', $data) ?: null);

        return parent::beforeSave($object);
    }
}
