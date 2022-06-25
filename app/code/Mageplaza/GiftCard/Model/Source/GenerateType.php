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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class GenerateType
 * @package Mageplaza\GiftCard\Model\Source
 */
class GenerateType implements OptionSourceInterface
{
    const AUTO   = 'auto';
    const IMPORT = 'import';
    const MANUAL = 'manual';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::AUTO   => __('Auto Generate'),
            self::IMPORT => __('Import'),
            self::MANUAL => __('Manual'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
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
