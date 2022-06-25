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

use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource;

/**
 * Class Year
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Year extends AbstractResource
{
    /**
     * @var string
     */
    const KEY = 'year';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_year', 'start_date');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable()
    {
        $maxYearDateStr = $this->getConnection()->fetchOne('SELECT MAX(end_date) FROM ' . $this->getMainTable());
        $fromDate = $this->getFromDate($maxYearDateStr);
        $toDate = $this->getToDate();

        $intervals = [];
        // If main table is empty
        if (!$maxYearDateStr) {
            $fromDate->setDate((integer)$fromDate->format('Y'), 1, 1);
        }
        while ($fromDate < $toDate) {
            // If main table is empty
            if (!$maxYearDateStr) {
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->setDate((integer)$fromDate->format('Y'), 12, 1);
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->setDate((integer)$fromDate->format('Y'), 1, 1);
                $fromDate->modify('+1 year');
                $fromDate->modify('first day of');
            } else {
                $fromDate->modify('+1 year');
                $fromDate->modify('first day of');
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->setDate((integer)$fromDate->format('Y'), 12, 1);
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->setDate((integer)$fromDate->format('Y'), 1, 1);
            }

            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }

        $this->addPeriodToTable($this->getMainTable(), $intervals);
    }
}
