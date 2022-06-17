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

namespace Mageplaza\GiftCard\Model\GiftCard;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Gift Card status functionality model
 */
class Status implements OptionSourceInterface
{
    /**
     * Gift Card Status
     */
    const STATUS_ACTIVE    = 1;
    const STATUS_INACTIVE  = 2;
    const STATUS_PENDING   = 3;
    const STATUS_USED      = 4;
    const STATUS_EXPIRED   = 5;
    const STATUS_CANCELLED = 6;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::STATUS_ACTIVE    => __('Active'),
            self::STATUS_INACTIVE  => __('Inactive'),
            self::STATUS_PENDING   => __('Pending'),
            self::STATUS_USED      => __('Used'),
            self::STATUS_EXPIRED   => __('Expired'),
            self::STATUS_CANCELLED => __('Cancelled')
        ];
    }

    /**
     * Retrieve option array for form create
     *
     * @return array
     */
    public static function getOptionArrayForForm()
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive')
        ];
    }

    /**
     * @return array
     */
    public static function getStatus()
    {
        return array_keys(self::getOptionArray());
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
