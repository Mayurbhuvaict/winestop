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

















































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Toolbar;

use Aheadworks\AdvancedReports\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\Period\RangeResolver as PeriodRangeResolver;
use Magento\Ui\Component\Container;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;

/**
 * Class PeriodRange
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Toolbar
 */
class PeriodRange extends Container
{
    /**
     * @var PeriodRangeResolver
     */
    private $periodRangeResolver;

    /**
     * @var PeriodSource
     */
    private $periodSource;

    /**
     * @var CompareSource
     */
    private $compareSource;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param PeriodSource $periodSource
     * @param CompareSource $compareSource
     * @param PeriodRangeResolver $periodRangeResolver
     * @param TimezoneInterface $localeDate
     * @param Config $config
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        PeriodSource $periodSource,
        CompareSource $compareSource,
        PeriodRangeResolver $periodRangeResolver,
        TimezoneInterface $localeDate,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->periodSource = $periodSource;
        $this->compareSource = $compareSource;
        $this->periodRangeResolver = $periodRangeResolver;
        $this->localeDate = $localeDate;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $dataProvider = $this->context->getDataProvider();
        $periodFilter = $dataProvider->getDefaultFilterPool()->getFilter('period');
        $isAvailableCompareTo = $dataProvider->isAvailableCompareTo();
        $config = $this->getData('config');

        $timezone = $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $config['compareAvailable'] = $isAvailableCompareTo;
        $config['defaultCompareType'] = $periodFilter->getDefaultCompareType();
        $config['dateRangeOptions'] = $this->getDateRangeOptions();
        $config['compareDateRangeOptions'] = $this->getCompareDateRangeOptions();
        $config['earliestDate'] = $this->getEarliestDate($timezone);
        $config['latestDate'] = $this->getLatestDate($timezone);
        $config['weekOffset'] = (int)$this->config->getFirstDayOfWeek();
        $config['dateFrom'] = $periodFilter->getPeriodFrom()->format('M d, Y');
        $config['dateTo'] = $periodFilter->getPeriodTo()->format('M d, Y');
        $config['dateRange'] = $periodFilter->getPeriodType();
        $config['compareEnabled'] = $dataProvider->isEnabledCompareTo();
        $config['compareDateFrom'] = $this->prepareCompareDate($periodFilter->getCompareFrom(), $isAvailableCompareTo);
        $config['compareDateTo'] = $this->prepareCompareDate($periodFilter->getCompareTo(), $isAvailableCompareTo);
        $config['compareDateRange'] = $isAvailableCompareTo
            ? $periodFilter->getCompareType()
            : $periodFilter->getDefaultCompareType();

        $this->setData('config', $config);
    }

    /**
     * Prepare compare date
     *
     * @param \DateTime $date
     * @param bool $isAvailableCompareTo
     * @return string|null
     */
    private function prepareCompareDate($date, $isAvailableCompareTo)
    {
        $compareDateFrom = empty($date)
            ? $date
            : $date->format('M d, Y');

        return $isAvailableCompareTo ? $compareDateFrom : null;
    }

    /**
     * Retrieve date range options
     *
     * @return array
     */
    private function getDateRangeOptions()
    {
        $options = $this->getRangeOptionsBySource($this->periodSource);
        $options = array_merge(
            $options,
            [['value' => PeriodSource::PERIOD_TYPE_CUSTOM, 'label' => __('Custom Date Range')]]
        );

        return $options;
    }

    /**
     * Retrieve compare date range options
     *
     * @return array
     */
    private function getCompareDateRangeOptions()
    {
        $options = $this->compareSource->toOptionArray();
        $options = array_merge(
            [['value' => PeriodSource::PERIOD_TYPE_CUSTOM, 'label' => __('Custom')]],
            $options
        );

        return $options;
    }

    /**
     * Retrieve first calendar date
     *
     * @param string $timezone
     * @return string
     */
    private function getEarliestDate($timezone)
    {
        $date = new \DateTime($this->config->getFirstAvailableDate(), new \DateTimeZone($timezone));

        return $date->format('Y-m-d');
    }

    /**
     * Retrieve latest calendar date
     *
     * @param string $timezone
     * @return string
     */
    private function getLatestDate($timezone)
    {
        $date = new \DateTime('now', new \DateTimeZone($timezone));

        return $date->format('Y-m-d');
    }

    /**
     * Retrieve date range options
     *
     * @param OptionSourceInterface $source
     * @return array
     */
    private function getRangeOptionsBySource($source)
    {
        $options = $source->toOptionArray();

        foreach ($options as &$option) {
            $periodType = $option['value'];
            $periods = $this->periodRangeResolver->resolve($periodType);

            list($option['from'], $option['to']) = $this->preparePeriod($periods['from'], $periods['to']);
            list($option['cFrom'], $option['cTo']) = $this->preparePeriod($periods['c_from'], $periods['c_to']);
            list($option['cYearFrom'], $option['cYearTo']) = $this->preparePeriod(
                $periods['c_year_from'],
                $periods['c_year_to']
            );
        }

        return $options;
    }

    /**
     * Prepare period
     *
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @return array
     */
    private function preparePeriod($periodFrom, $periodTo)
    {
        return [$periodFrom->format('M d, Y'), $periodTo->format('M d, Y')];
    }
}
