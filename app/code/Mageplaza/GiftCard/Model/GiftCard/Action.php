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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\Pool;

/**
 * Gift Card action functionality model
 */
class Action implements OptionSourceInterface
{
    /**
     * Gift Card Status
     */
    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_SEND   = 3;
    const ACTION_SPEND  = 4;
    const ACTION_EXPIRE = 5;
    const ACTION_REDEEM = 6;
    const ACTION_REFUND = 7;
    const ACTION_REVERT = 8;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::ACTION_CREATE => __('Created'),
            self::ACTION_UPDATE => __('Updated'),
            self::ACTION_SEND   => __('Sent'),
            self::ACTION_SPEND  => __('Spent'),
            self::ACTION_EXPIRE => __('Expired'),
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
     * @param $type
     * @param $params
     *
     * @return Phrase|string
     */
    public static function getActionLabel($type, $params)
    {
        $message = '';

        if (!$params) {
            return $message;
        }

        if (!is_array($params)) {
            $params = Data::jsonDecode($params);
        }

        switch ($type) {
            case self::ACTION_CREATE:
                if (isset($params['auth'])) {
                    if (isset($params['order_increment_id'])) {
                        $message = __('Created by: %1. Order: #%2', $params['auth'], $params['order_increment_id']);
                    } else {
                        $message = __('Created by admin: %1', $params['auth']);
                    }
                } elseif (isset($params['pool_id'])) {
                    $pool = ObjectManager::getInstance()->create(Pool::class);
                    $pool->load($params['pool_id']);
                    if ($pool->getId()) {
                        $message = __('Created from Pool: %1', $pool->getName());
                    }
                }

                $message = $message ?: __('Created');
                break;
            case self::ACTION_UPDATE:
                $message = __('Updated by: %1.', $params['auth']);
                break;
            case self::ACTION_SEND:
                if (isset($params['auth'])) {
                    $message = __('Sent to customer by %1', $params['auth']);
                } else {
                    $message = __('Sent to customer');
                }
                break;
            case self::ACTION_REDEEM:
                $message = __('Redeemed by: %1', $params['auth']);
                break;
            case self::ACTION_EXPIRE:
                $message = __('Expired');
                break;
            case self::ACTION_REFUND:
                $message = __('Order Refunded #%1', $params['order_increment_id']);
                break;
            case self::ACTION_REVERT:
                $message = __('Order #%1', $params['order_increment_id']);
                break;
            case self::ACTION_SPEND:
                $message = __('Spend for order #%1 by %2', $params['order_increment_id'], $params['auth']);
                break;
            default:
                break;
        }

        return $message;
    }
}
