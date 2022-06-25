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

namespace Mageplaza\GiftCard\Model\Transaction;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Gift Card action functionality model
 */
class Action implements OptionSourceInterface
{
    /**
     * Gift Card Status
     */
    const ACTION_ADMIN  = 1;
    const ACTION_REDEEM = 2;
    const ACTION_SPEND  = 3;
    const ACTION_REFUND = 4;
    const ACTION_REVERT = 5;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::ACTION_ADMIN  => __('Admin'),
            self::ACTION_SPEND  => __('Spent'),
            self::ACTION_REDEEM => __('Redeemed'),
            self::ACTION_REFUND => __('Refunded'),
            self::ACTION_REVERT => __('Reverted')
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

    /**
     * Get Action detail
     *
     * @param $action
     * @param $extraContent
     *
     * @return Phrase|string
     */
    public static function getActionDetail($action, $extraContent)
    {
        $message = '';
        if (!$extraContent) {
            return $message;
        }
        if (!is_array($extraContent)) {
            $extraContent = Data::jsonDecode($extraContent);
        }

        switch ($action) {
            case self::ACTION_ADMIN:
                $message = isset($extraContent['auth']) ? __(
                    'Changed By: %1',
                    $extraContent['auth']
                ) : __('Changed By Admin');
                break;
            case self::ACTION_REDEEM:
                $message = isset($extraContent['code']) ? __(
                    'Redeemed From: %1',
                    $extraContent['code']
                ) : __('Redeemed');
                break;
            case self::ACTION_SPEND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Spend for order #%1',
                    $extraContent['order_increment_id']
                ) : __('Spent');
                break;
            case self::ACTION_REFUND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Refund on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Refund');
                break;
            case self::ACTION_REVERT:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Revert on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Revert');
                break;
            default:
                break;
        }

        return $message;
    }

    /**
     * @param $action
     * @param $extraContent
     *
     * @return Phrase|string
     */
    public static function getActionLabel($action, $extraContent)
    {
        $message = '';
        if (!$extraContent) {
            return $message;
        }
        if (!is_array($extraContent)) {
            $extraContent = Data::jsonDecode($extraContent);
        }

        switch ($action) {
            case self::ACTION_ADMIN:
                $message = isset($extraContent['auth']) ? __(
                    'Changed By: %1',
                    $extraContent['auth']
                ) : __('Changed By Admin');
                break;
            case self::ACTION_REDEEM:
                $message = isset($extraContent['code']) ? __(
                    'Redeemed From: %1',
                    $extraContent['code']
                ) : __('Redeemed');
                break;
            case self::ACTION_SPEND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Spend for order #%1',
                    $extraContent['order_increment_id']
                ) : __('Spent');
                break;
            case self::ACTION_REFUND:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Refund on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Refund');
                break;
            case self::ACTION_REVERT:
                $message = isset($extraContent['order_increment_id']) ? __(
                    'Revert on order #%1',
                    $extraContent['order_increment_id']
                ) : __('Revert');
                break;
            default:
                break;
        }

        return $message;
    }
}
