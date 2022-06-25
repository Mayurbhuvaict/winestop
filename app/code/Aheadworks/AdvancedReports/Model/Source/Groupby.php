<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */





























































































































































namespace Aheadworks\AdvancedReports\Model\Source;

/**
 * Class Groupby
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class Groupby implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_DAY = 'day';
    const TYPE_WEEK = 'week';
    const TYPE_MONTH = 'month';
    const TYPE_QUARTER = 'quarter';
    const TYPE_YEAR = 'year';
    /**#@-*/

    /**
     * To option array
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_DAY, 'label' => __('Day')],
            ['value' => self::TYPE_WEEK, 'label' => __('Week')],
            ['value' => self::TYPE_MONTH, 'label' => __('Month')],
            ['value' => self::TYPE_QUARTER, 'label' => __('Quarter')],
            ['value' => self::TYPE_YEAR, 'label' => __('Year')],
        ];
    }
}
