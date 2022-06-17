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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class FieldRenderer
 * @package Mageplaza\GiftCard\Model\Source
 */
class FieldRenderer extends AbstractModel implements ArrayInterface
{
    const AMOUNT    = 'amount';
    const METHOD    = 'delivery_method';
    const ADDRESS   = 'delivery_address';
    const DATE      = 'delivery_date';
    const TIMEZONE  = 'timezone';
    const SENDER    = 'sender';
    const RECIPIENT = 'recipient';
    const MESSAGE   = 'message';
    const TEMPLATE  = 'template';
    const IMAGE     = 'image';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::AMOUNT    => __('Gift Card Amount'),
            self::SENDER    => __('Sender'),
            self::RECIPIENT => __('Recipient'),
            self::METHOD    => __('Delivery Method'),
            self::ADDRESS   => __('Delivery To'),
            self::MESSAGE   => __('Message'),
            self::TEMPLATE  => __('Template'),
            self::DATE      => __('Delivery Date')
        ];
    }

    /**
     * @return array
     */
    public static function getFullOptionArray()
    {
        return array_merge(self::getOptionArray(), [
            self::IMAGE    => __('Template Image'),
            self::TIMEZONE => __('Timezone')
        ]);
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
