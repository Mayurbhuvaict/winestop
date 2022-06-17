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
















































namespace Aheadworks\AdvancedReports\Model\Email\Report;

use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\Period\RangeResolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Locale\Resolver as LocalResolver;

/**
 * Class PeriodResolver
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report
 */
class PeriodResolver
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var RangeResolver
     */
    private $rangeResolver;

    /**
     * @var LocalResolver
     */
    private $localResolver;

    /**
     * @param TimezoneInterface $localeDate
     * @param RangeResolver $rangeResolver
     * @param LocalResolver $localResolver
     */
    public function __construct(
        TimezoneInterface $localeDate,
        RangeResolver $rangeResolver,
        LocalResolver $localResolver
    ) {
        $this->localeDate = $localeDate;
        $this->rangeResolver = $rangeResolver;
        $this->localResolver = $localResolver;
    }

    /**
     * Resolve periods
     *
     * @param ConfigInterface $reportConfig
     * @return array
     */
    public function getPeriods(ConfigInterface $reportConfig)
    {
        $period = $this->rangeResolver->resolve($reportConfig->getWhenToSendFrequency());

        $from = $period['from'];
        $to = $period['to'];

        return [$from, $to];
    }

    /**
     * Resolve periods formatted
     *
     * @param ConfigInterface $reportConfig
     * @return array
     */
    public function getPeriodsFormatted(ConfigInterface $reportConfig)
    {
        $period = $this->rangeResolver->resolve($reportConfig->getWhenToSendFrequency());

        $from = $period['from'];
        $to = $period['to'];

        return [$this->formatDate($from), $this->formatDate($to)];
    }

    /**
     * Format date
     *
     * @param \DateTime $date
     * @return string
     */
    private function formatDate($date)
    {
        return $this->localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::NONE,
            $this->localResolver->getLocale()
        );
    }
}
