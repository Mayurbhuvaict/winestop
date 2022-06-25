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

namespace Mageplaza\GiftCard\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class GenerateGiftCodeEvent
 * @package Mageplaza\GiftCard\Model\Source
 */
class GenerateGiftCodeEvent implements ArrayInterface
{
    const ORDER_PLACED    = '1';
    const INVOICED        = '2';
    const ORDER_COMPLETED = '3';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            //          self::ORDER_PLACED    => __('Order Placed'),
            self::INVOICED        => __('Invoice Created'),
            self::ORDER_COMPLETED => __('Order Completed')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
