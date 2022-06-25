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






























































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor;

use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor
 */
class Period extends DataObject implements MergerInterface
{
    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @param PeriodModel $periodModel
     * @param array $data
     */
    public function __construct(
        PeriodModel $periodModel,
        array $data = []
    ) {
        parent::__construct($data);
        $this->periodModel = $periodModel;
    }

    /**
     * {@inheritdoc}
     */
    public function merge($rows, $compareRows, $dataSourceData)
    {
        list($rows, $compareRows) = $this->prepareRows($rows, $compareRows, $dataSourceData);
        $groupBy = $dataSourceData['groupByFilter'];
        $intervals = $this->getMergedIntervals(
            $groupBy,
            $dataSourceData['periodFromFilter'],
            $dataSourceData['comparePeriodFromFilter'],
            count($rows)
        );
        $rows = $this->mergeArray($rows, $compareRows, $groupBy, $intervals);

        return $rows;
    }

    /**
     * Merge data from two arrays
     *
     * @param array $rows
     * @param array $compareRows
     * @param string $groupBy
     * @param array $intervals
     * @return array
     */
    private function mergeArray($rows, $compareRows, $groupBy, $intervals)
    {
        foreach ($compareRows as $compareRowIndex => $compareRowValue) {
            //$rowIndex = $this->getRowIndex($rows, $compareRowValue, $groupBy, $intervals);
            $rowIndex = $compareRowIndex;
            foreach ($compareRowValue as $index => $value) {
                $rows[$rowIndex]['c_' . $index] = $value;
            }
        }

        return $rows;
    }

    /**
     * Retrieve merged intervals
     *
     * @param string $groupBy
     * @param \DateTime $periodFrom
     * @param \DateTime $comparePeriodFrom
     * @param int $intervalsCount
     * @return array
     * @throws LocalizedException
     */
    private function getMergedIntervals($groupBy, $periodFrom, $comparePeriodFrom, $intervalsCount)
    {
        $periods = $this->periodModel->getPeriods($periodFrom, $intervalsCount, $groupBy);
        $comparePeriods = $this->periodModel->getPeriods($comparePeriodFrom, $intervalsCount, $groupBy);

        $compareIntervals = $comparePeriods['intervals'];
        $intervals = $periods['intervals'];
        foreach ($intervals as $key => &$interval) {
            if ($groupBy == GroupbySource::TYPE_DAY) {
                $interval['c_date'] = $compareIntervals[$key]['date'];
            } else {
                $interval['c_start_date'] = $compareIntervals[$key]['start_date'];
                $interval['c_end_date'] = $compareIntervals[$key]['end_date'];
            }
        }

        return $intervals;
    }

    /**
     * Retrieve searched row index
     *
     * @param array $rows
     * @param array $compareRowValue
     * @param string $groupBy
     * @param array $intervals
     * @return int|null
     */
    private function getRowIndex($rows, $compareRowValue, $groupBy, $intervals)
    {
        $intervalMatched = false;
        $dateFieldName = $groupBy == GroupbySource::TYPE_DAY ? 'date' : 'start_date';
        foreach ($intervals as $interval) {
            if ($interval['c_' . $dateFieldName] == $compareRowValue[$dateFieldName]) {
                $intervalMatched = $interval;
                break;
            }
        }
        foreach ($rows as $index => $row) {
            if ($row[$dateFieldName] == $intervalMatched[$dateFieldName]) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Prepare rows data
     *
     * @param array $rows
     * @param array $compareRows
     * @param array $dataSourceData
     * @return array
     * @throws LocalizedException
     */
    private function prepareRows($rows, $compareRows, $dataSourceData)
    {
        if (count($rows) < count($compareRows)) {
            $groupBy = $dataSourceData['groupByFilter'];
            $periodFrom = $dataSourceData['periodFromFilter'];

            $rows = $this->addMissingPeriods($rows, $compareRows, $groupBy, $periodFrom);
        }
        return [$rows, $compareRows];
    }

    /**
     * Add missing periods
     *
     * @param array $rows
     * @param array $compareRows
     * @param string $groupBy
     * @param \DateTime $periodFrom
     * @return array
     * @throws LocalizedException
     */
    private function addMissingPeriods($rows, $compareRows, $groupBy, $periodFrom)
    {
        $intervalsCount = count($compareRows);
        $periods = $this->periodModel->getPeriods($periodFrom, $intervalsCount, $groupBy);

        foreach ($periods['intervals'] as $index => $interval) {
            $rows[$index]['not_change_end_date'] = true;
            if ($periods['period'] == GroupbySource::TYPE_DAY) {
                if (!$this->isNeedAddingPeriod($rows, $interval, $index, 'date')) {
                    continue;
                }
                $rows[$index]['date'] = $interval['date'];
            } else {
                if (!$this->isNeedAddingPeriod($rows, $interval, $index, 'start_date')) {
                    continue;
                }
                $rows[$index]['start_date'] = $interval['start_date'];
                $rows[$index]['end_date'] = $interval['end_date'];
            }
        }

        return $rows;
    }

    /**
     * Check if need adding period
     *
     * @param array $rows
     * @param array $interval
     * @param int $index
     * @param string $field
     * @return bool
     */
    private function isNeedAddingPeriod($rows, $interval, $index, $field)
    {
        return !(isset($rows[$index][$field]) && $rows[$index][$field] == $interval[$field]);
    }
}
