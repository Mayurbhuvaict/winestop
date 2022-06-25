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































namespace Aheadworks\AdvancedReports\Model;

use Aheadworks\AdvancedReports\Model\Source\Groupby;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Period
{
    /**
     * @var DatesGroupingFactory
     */
    private $datesGroupingFactory;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param DatesGroupingFactory $datesGroupingFactory
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        DatesGroupingFactory $datesGroupingFactory,
        TimezoneInterface $localeDate
    ) {
        $this->datesGroupingFactory = $datesGroupingFactory;
        $this->localeDate = $localeDate;
    }

    /**
     * Retrieve resolved period dates
     *
     * @param array $item
     * @param string $groupBy
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param bool $isCompare
     * @return array|null
     */
    public function periodDatesResolve($item, $groupBy, $periodFrom, $periodTo, $isCompare)
    {
        $prefix = $isCompare ? 'c_' : '';
        $startDate = $endDate = '';
        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        switch ($groupBy) {
            case GroupbySource::TYPE_DAY:
                if (isset($item[$prefix . 'date'])) {
                    $startDate = $endDate = new \DateTime($item[$prefix . 'date'], new \DateTimeZone($timezone));
                }
                break;
            case GroupbySource::TYPE_WEEK:
            case GroupbySource::TYPE_MONTH:
            case GroupbySource::TYPE_QUARTER:
            case GroupbySource::TYPE_YEAR:
                if (isset($item[$prefix . 'start_date'])) {
                    $startDate = new \DateTime($item[$prefix . 'start_date'], new \DateTimeZone($timezone));
                }
                if (isset($item[$prefix . 'end_date'])) {
                    $endDate = new \DateTime($item[$prefix . 'end_date'], new \DateTimeZone($timezone));
                }
                break;
        }
        if (empty($startDate) || empty($endDate)) {
            return null;
        }

        $notChangeEndDate = isset($item['not_change_end_date']) && $item['not_change_end_date'];
        $startDate = $startDate < $periodFrom ? $periodFrom : $startDate;
        $endDate = $endDate > $periodTo && !$notChangeEndDate ? $periodTo : $endDate;

        return ['start_date' => $startDate, 'end_date' => $endDate];
    }

    /**
     * Get periods between dates
     *
     * @param \DateTime $from
     * @param int $intervalsCount
     * @param string $groupBy
     * @return array
     */
    public function getPeriods($from, $intervalsCount, $groupBy)
    {
        $result = ['period' => $groupBy, 'intervals' => []];
        try {
            $datePeriod = $this->datesGroupingFactory->create($groupBy);
            $intervals = $datePeriod->getPeriods($from, $intervalsCount);
            $result['intervals'] = $intervals;
        } catch (LocalizedException $e) {
            return $result;
        }
        return $result;
    }

    /**
     * Retrieve period as string
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $groupBy
     * @param bool $isShowYear
     * @return string
     */
    public function getPeriodAsString($from, $to, $groupBy, $isShowYear = true)
    {
        $value = '';
        switch ($groupBy) {
            case Groupby::TYPE_DAY:
                $value = $this->formatDate($from, $isShowYear);
                break;
            case Groupby::TYPE_WEEK:
                $value = $this->formatDate($from, $isShowYear) . ' - ' . $this->formatDate($to, $isShowYear);
                break;
            case Groupby::TYPE_MONTH:
                $value = $from->format($isShowYear ? 'M Y' : 'M');
                break;
            case Groupby::TYPE_QUARTER:
                $month = (integer)$from->format('m');
                $value = 'Q' . ceil($month / 3) . ' ' . $from->format('Y');
                break;
            case Groupby::TYPE_YEAR:
                $value = $from->format('Y');
                break;
        }
        return $value;
    }

    /**
     * Retrieve formatted date
     *
     * @param \DateTime $date
     * @param boolean $isShowYear
     * @return string
     */
    private function formatDate($date, $isShowYear)
    {
        $pattern = $isShowYear ? 'M d, Y' : 'M d';
        return $date->format($pattern);
    }
}
