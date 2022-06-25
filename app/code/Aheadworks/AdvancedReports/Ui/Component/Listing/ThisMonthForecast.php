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





















































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class ThisMonthForecast
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class ThisMonthForecast extends Container
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ContextInterface $context
     * @param TimezoneInterface $localeDate
     * @param ScopeConfigInterface $scopeConfig
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        TimezoneInterface $localeDate,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->localeDate = $localeDate;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $periodFilter = $this->context->getDataProvider()->getDefaultFilterPool()->getFilter('period');
        if (!$periodFilter->isThisMonthForecastEnabled()) {
            return parent::prepareDataSource($dataSource);
        }

        $columns = $this->getData('config/columns') ?: [];
        $dataSource['data']['thisMonthForecast'] = $this->getThisMonthForecast($columns);
        return parent::prepareDataSource($dataSource);
    }

    /**
     * Retrieve this month forecast
     *
     * @param array $columns
     * @return array
     */
    private function getThisMonthForecast($columns)
    {
        $monthToDateItems = $this->getMonthToDateItems();
        list($workDayList, $weekendDayList) = $this->getWorkAndWeekendItems();
        list($workDayLeft, $weekendDayLeft) = $this->getWorkAndWeekendDayLeft();

        $thisMonthForecast = [];
        foreach ($columns as $column) {
            $workDayListByColumn = $this->getItemsByColumn($workDayList, $column);
            $workMedian = $this->getMedianFromArray($workDayListByColumn);

            $weekendDayListByColumn = $this->getItemsByColumn($weekendDayList, $column);
            $weekendMedian = $this->getMedianFromArray($weekendDayListByColumn);

            $thisMonthSoFar = $this->getItemsByColumn($monthToDateItems, $column);
            $thisMonthForecast[$column] =
                array_sum($thisMonthSoFar) + $workMedian * $workDayLeft + $weekendMedian * $weekendDayLeft;
        }

        return $thisMonthForecast;
    }

    /**
     * Retrieve items by column name
     *
     * @param array $items
     * @param string $column
     * @return array
     */
    private function getItemsByColumn($items, $column)
    {
        $preparedItems = [];
        foreach ($items as $item) {
            $preparedItems[] = $item[$column];
        }

        return $preparedItems;
    }

    /**
     * Retrieve work and weekend items
     *
     * @return array
     */
    private function getWorkAndWeekendItems()
    {
        $lastTwoWeeksItems = $this->getLastTwoWeeksItems();
        $weekendDays = $this->getWeekendDays();
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        $workDayList = $weekendDayList = [];
        foreach ($lastTwoWeeksItems as $item) {
            $date = new \DateTime($item['date'], new \DateTimeZone($timezone));
            $weekdayNumber = (int)$date->format('w');
            $isWeekend = in_array($weekdayNumber, $weekendDays);

            if ($isWeekend) {
                $weekendDayList[] = $item;
            } else {
                $workDayList[] = $item;
            }
        }

        return [$workDayList, $weekendDayList];
    }

    /**
     * Retrieve work and weekend day left
     *
     * @return array
     */
    private function getWorkAndWeekendDayLeft()
    {
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $today = new \DateTime('today', new \DateTimeZone($timezone));
        $weekendDays = $this->getWeekendDays();
        $startDateNumber = (int)$today->format('j');
        $daysNumberInMonth = (int)$today->format('t');

        $workDayLeft = $weekendDayLeft = 0;
        for ($day = $startDateNumber; $day <= $daysNumberInMonth; $day++) {
            $weekdayNumber = (int)$today->format('w');
            $isWeekend = in_array($weekdayNumber, $weekendDays);

            $isWeekend ? $weekendDayLeft++ : $workDayLeft++;
            $today->modify('+1 day');
        }

        return [$workDayLeft, $weekendDayLeft];
    }

    /**
     * Retrieve start forecast date
     *
     * @return \DateTime
     */
    private function getDateStartForecast()
    {
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $today = new \DateTime('today', new \DateTimeZone($timezone));
        $yesterday = new \DateTime('yesterday', new \DateTimeZone($timezone));

        // check number of the month
        return $yesterday->format('n') == $today->format('n') ? $yesterday : $today;
    }

    /**
     * Retrieve month to date items
     *
     * @return array
     */
    private function getMonthToDateItems()
    {
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $firstDayOfMonth = new \DateTime('first day of this month', new \DateTimeZone($timezone));
        $dateStartForecast = $this->getDateStartForecast();

        $items = [];
        if ($dateStartForecast > $firstDayOfMonth) {
            $searchResult = $this->getContext()->getDataProvider()->getSearchResultCached();
            $items = $searchResult->getItemsByCustomPeriod(
                $firstDayOfMonth->format(DateTime::DATE_PHP_FORMAT),
                $dateStartForecast->format(DateTime::DATE_PHP_FORMAT)
            );
        }

        return $items;
    }

    /**
     * Retrieve last two weeks items
     *
     * @return array
     */
    private function getLastTwoWeeksItems()
    {
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $twoWeeksAgoDate = new \DateTime('14 days ago', new \DateTimeZone($timezone));
        $yesterday = new \DateTime('yesterday', new \DateTimeZone($timezone));

        $searchResult = $this->getContext()->getDataProvider()->getSearchResultCached();
        $items = $searchResult->getItemsByCustomPeriod(
            $twoWeeksAgoDate->format(DateTime::DATE_PHP_FORMAT),
            $yesterday->format(DateTime::DATE_PHP_FORMAT)
        );

        return $items;
    }

    /**
     * Retrieve median value from array
     *
     * @param array $data
     * @return float
     */
    private function getMedianFromArray($data)
    {
        if (count($data) === 0) {
            return 0;
        }
        sort($data);
        $dataCount = count($data);
        $middleValue = (int)floor($dataCount / 2);
        if ($dataCount % 2 === 1) {
            return (float)$data[$middleValue];
        }

        return ($data[$middleValue - 1] + $data[$middleValue]) / 2;
    }

    /**
     * Retrieve weekend days number
     *
     * @return array
     */
    private function getWeekendDays()
    {
        return explode(',', $this->scopeConfig->getValue('general/locale/weekend'));
    }
}
