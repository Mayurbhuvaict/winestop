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










































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

/**
 * Class Week
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Week extends AbstractResource
{
    /**
     * @var string
     */
    const KEY = 'week';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_weeks', 'start_date');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable()
    {
        $maxWeekDateStr = $this->getConnection()->fetchOne('SELECT MAX(end_date) FROM ' . $this->getMainTable());
        $fromDate = $this->getFromDate($maxWeekDateStr);
        $toDate = $this->getToDate();
        $intervals = [];

        // If main table is empty
        if (!$maxWeekDateStr) {
            $firstWeekDay = $this->getLocaleFirstDay();
            $curWeekDay = $fromDate->format('N');
            if ($curWeekDay > $firstWeekDay) {
                $firstWeekDay += 6;
            }
            $diff = abs($curWeekDay - $firstWeekDay);
            $startDate = $fromDate->format('Y-m-d');
            $fromDate->modify('+' . $diff . ' day');
            $endDate = $fromDate->format('Y-m-d');
            $fromDate->modify('+1 day');
            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }
        while ($fromDate < $toDate) {
            // If main table is empty
            if (!$maxWeekDateStr) {
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 week')->modify('-1 day');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 day');
            } else {
                $fromDate->modify('+1 day');
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 week')->modify('-1 day');
                $endDate = $fromDate->format('Y-m-d');
            }

            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }
        $this->addPeriodToTable($this->getMainTable(), $intervals);
    }

    /**
     * Get locale first day
     *
     * @return string
     */
    private function getLocaleFirstDay()
    {
        return $this->scopeConfig->getValue('general/locale/firstday');
    }
}
