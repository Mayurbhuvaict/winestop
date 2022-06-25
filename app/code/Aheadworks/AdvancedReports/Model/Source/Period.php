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

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class Period implements OptionSourceInterface
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_TODAY = 'today';
    const TYPE_YESTERDAY = 'yesterday';
    const TYPE_LAST_7_DAYS = 'last_7_days';
    const TYPE_LAST_WEEK = 'last_week';
    const TYPE_LAST_BUSINESS_WEEK = 'last_business_week';
    const TYPE_WEEK_TO_DATE = 'week_to_date';
    const TYPE_MONTH_TO_DATE = 'month_to_date';
    const TYPE_LAST_MONTH = 'last_month';
    const TYPE_LAST_QUARTER = 'last_quarter';
    const PERIOD_TYPE_CUSTOM = 'custom';
    /**#@-*/

    /**
     * @var array
     */
    private $options;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ListsInterface
     */
    private $localeList;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ListsInterface $localeList
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ListsInterface $localeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeList = $localeList;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $firstDayOfWeek = $this->scopeConfig->getValue('general/locale/firstday');
            $lastDayOfWeek = $firstDayOfWeek + 6 > 6
                ? 0
                : $firstDayOfWeek + 6;
            $businessWeekdays = $this->getBusinessWeekdays();

            $this->options = [
                ['value' => self::TYPE_TODAY, 'label' => __('Today')],
                ['value' => self::TYPE_YESTERDAY, 'label' => __('Yesterday')],
                ['value' => self::TYPE_WEEK_TO_DATE, 'label' => __('Week to Date')],
                ['value' => self::TYPE_LAST_7_DAYS, 'label' => __('Last 7 Days')],
                [
                    'value' => self::TYPE_LAST_WEEK,
                    'label' => __('Last Week')
                        . ' (' . $this->getWeekdaysRangeLabel($firstDayOfWeek, $lastDayOfWeek) . ')'
                ],
                [
                    'value' => self::TYPE_LAST_BUSINESS_WEEK,
                    'label' => __('Last Business Week')
                        . ' (' . $this->getWeekdaysRangeLabel(reset($businessWeekdays), end($businessWeekdays)) . ')'
                ],
                ['value' => self::TYPE_MONTH_TO_DATE, 'label' => __('Month to Date')],
                ['value' => self::TYPE_LAST_MONTH, 'label' => __('Last Month')],
                ['value' => self::TYPE_LAST_QUARTER, 'label' => __('Last Quarter')],
            ];
        }

        return $this->options;
    }

    /**
     * Get weekdays keyed by index
     *
     * @return array
     */
    private function getWeekdaysKeyedByIndex()
    {
        $weekdaysKeyedByIndex = [];
        foreach ($this->localeList->getOptionWeekdays() as $weekday) {
            $weekdaysKeyedByIndex[$weekday['value']] = $weekday['label'];
        }
        return $weekdaysKeyedByIndex;
    }

    /**
     * Get business weekdays
     *
     * @return array
     */
    private function getBusinessWeekdays()
    {
        $weekdays = array_keys($this->getWeekdaysKeyedByIndex());
        $weekendDays = explode(',', $this->scopeConfig->getValue('general/locale/weekend'));
        return array_diff($weekdays, $weekendDays);
    }

    /**
     * Get weekdays range label
     *
     * @param int $first
     * @param int $last
     * @return string
     */
    private function getWeekdaysRangeLabel($first, $last)
    {
        $weekdays = $this->getWeekdaysKeyedByIndex();
        return substr($weekdays[$first], 0, 3) . ' - ' . substr($weekdays[$last], 0, 3);
    }
}
