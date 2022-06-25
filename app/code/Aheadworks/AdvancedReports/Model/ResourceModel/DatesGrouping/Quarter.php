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
 * Class Quarter
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Quarter extends AbstractResource
{
    /**
     * @var string
     */
    const KEY = 'quarter';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_quarter', 'start_date');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable()
    {
        $maxQuarterDateStr = $this->getConnection()->fetchOne('SELECT MAX(end_date) FROM ' . $this->getMainTable());
        $fromDate = $this->getFromDate($maxQuarterDateStr);
        $toDate = $this->getToDate();

        $intervals = [];
        // If main table is empty
        if (!$maxQuarterDateStr) {
            $fromDate->setDate((integer)$fromDate->format('Y'), 1, 1);
        }
        while ($fromDate < $toDate) {
            // If main table is empty
            if (!$maxQuarterDateStr) {
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+2 month');
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->modify('first day of');
                $fromDate->modify('+1 month');
            } else {
                $fromDate->modify('first day of');
                $fromDate->modify('+1 month');
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+2 month');
                $fromDate->modify('last day of');
                $endDate = $fromDate->format('Y-m-d');
            }

            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }

        $this->addPeriodToTable($this->getMainTable(), $intervals);
    }
}
