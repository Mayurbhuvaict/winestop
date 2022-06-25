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

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Class AbstractClass
 * @package Mageplaza\GiftCard\Model\Attribute\Backend
 */
abstract class AbstractClass extends AbstractBackend
{
    /**
     * Returns whether the value is greater than, or equal to, zero
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isPositiveOrZero($value)
    {
        if (!is_numeric($value)) {
            return false;
        }

        $isNegative = $value < 0;

        return !$isNegative;
    }
}
