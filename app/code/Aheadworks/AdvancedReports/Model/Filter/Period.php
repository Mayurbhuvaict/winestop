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
































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\Period\RangeResolver as PeriodRangeResolver;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class Period implements FilterInterface
{
    /**
     * @var string
     */
    const PERIOD_SESSION_KEY = 'aw_arep_period';

    /**
     * @var string
     */
    const COMPARE_SESSION_KEY = 'aw_arep_compare_period';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var PeriodRangeResolver
     */
    protected $periodRangeResolver;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var array
     */
    private $periodCache;

    /**
     * @var array
     */
    private $comparePeriodCache;

    /**
     * @var bool
     */
    private $isCacheUsed;

    /**
     * @param RequestInterface $request
     * @param PeriodRangeResolver $periodRangeResolver
     * @param SessionManagerInterface $session
     * @param TimezoneInterface $localeDate
     * @param bool $isCacheUsed
     */
    public function __construct(
        RequestInterface $request,
        PeriodRangeResolver $periodRangeResolver,
        SessionManagerInterface $session,
        TimezoneInterface $localeDate,
        $isCacheUsed = false
    ) {
        $this->request = $request;
        $this->periodRangeResolver = $periodRangeResolver;
        $this->session = $session;
        $this->localeDate = $localeDate;
        $this->isCacheUsed = $isCacheUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return [
            'is_this_month_forecast_enabled' => $this->isThisMonthForecastEnabled(),
            'type' => $this->getPeriodType(),
            'from' => $this->getPeriodFrom(),
            'to' => $this->getPeriodTo(),
            'is_compare_enabled' => $this->isCompareEnabled(),
            'compare_type' => $this->getCompareType(),
            'compare_from' => $this->getCompareFrom(),
            'compare_to' => $this->getCompareTo(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return [
            'is_this_month_forecast_enabled' => null,
            'type' => $this->getDefaultPeriodType(),
            'from' => null,
            'to' => null,
            'is_compare_enabled' => false,
            'compare_type' => $this->getDefaultCompareType(),
            'compare_from' => null,
            'compare_to' => null,
        ];
    }

    /**
     * Check if this month forecast enabled
     *
     * @return bool
     */
    public function isThisMonthForecastEnabled()
    {
        return false;
    }

    /**
     * Retrieve period from
     *
     * @return \DateTime
     */
    public function getPeriodFrom()
    {
        $period = $this->getPeriod();
        return $period['from'];
    }

    /**
     * Retrieve period to
     *
     * @return \DateTime
     */
    public function getPeriodTo()
    {
        $period = $this->getPeriod();
        return $period['to'];
    }

    /**
     * Retrieve period type
     *
     * @return string
     */
    public function getPeriodType()
    {
        $period = $this->getPeriod();
        return $period['type'];
    }

    /**
     * Retrieve default period type
     *
     * @return string
     */
    public function getDefaultPeriodType()
    {
        return PeriodSource::TYPE_MONTH_TO_DATE;
    }

    /**
     * Is compare enabled
     *
     * @return bool
     */
    public function isCompareEnabled()
    {
        $period = $this->getComparePeriod();
        return $period['enabled'];
    }

    /**
     * Retrieve compare from
     *
     * @return \DateTime
     */
    public function getCompareFrom()
    {
        $period = $this->getComparePeriod();
        return $period['from'];
    }

    /**
     * Retrieve compare to
     *
     * @return \DateTime
     */
    public function getCompareTo()
    {
        $period = $this->getComparePeriod();
        return $period['to'];
    }

    /**
     * Retrieve compare type
     *
     * @return string
     */
    public function getCompareType()
    {
        $period = $this->getComparePeriod();
        return $period['type'];
    }

    /**
     * Retrieve default period type
     *
     * @return string
     */
    public function getDefaultCompareType()
    {
        return CompareSource::TYPE_PREVIOUS_PERIOD;
    }

    /**
     * Retrieve current compare period
     *
     * @return array
     */
    protected function getComparePeriod()
    {
        if (null !== $this->comparePeriodCache) {
            return $this->comparePeriodCache;
        }

        $this->comparePeriodCache = [
            'enabled' => false,
            'type' => $this->getDefaultCompareType(),
            'from' => null,
            'to' => null,
        ];
        $comparePeriodType = 'disabled';
        $sessionData = $this->session->getData(self::COMPARE_SESSION_KEY);

        $requestPeriodTypeParamValue = $this->request->getParam('compare_type');
        if ($requestPeriodTypeParamValue !== null) {
            $comparePeriodType = $requestPeriodTypeParamValue;
        } else {
            if ($sessionData !== null) {
                $comparePeriodType = $sessionData['type'];
            }
        }

        if ($comparePeriodType == 'disabled') {
            $this->comparePeriodCache['type'] = 'disabled';
            $this->session->setData(self::COMPARE_SESSION_KEY, $this->comparePeriodCache);
            $this->comparePeriodCache['type'] = $this->getDefaultCompareType();
            return $this->comparePeriodCache;
        }

        $this->comparePeriodCache['enabled'] = true;
        $this->comparePeriodCache['type'] = $comparePeriodType;
        $periodType = $this->getPeriodType();
        if ($comparePeriodType != PeriodSource::PERIOD_TYPE_CUSTOM
            && $periodType != PeriodSource::PERIOD_TYPE_CUSTOM) {
            $this->comparePeriodCache = array_merge(
                $this->comparePeriodCache,
                $this->resolveCompareRange($periodType, $comparePeriodType)
            );
        } else {
            $timezone = new \DateTimeZone($this->getLocaleTimezone());

            $requestFromParamValue = $this->request->getParam('compare_from');
            if ($requestFromParamValue !== null) {
                $this->comparePeriodCache['from'] = new \DateTime($requestFromParamValue, $timezone);
            } elseif (isset($sessionData['from'])) {
                $this->comparePeriodCache['from'] = $sessionData['from'];
            }
            $requestToParamValue = $this->request->getParam('compare_to');
            if ($requestToParamValue !== null) {
                $this->comparePeriodCache['to'] = new \DateTime($requestToParamValue, $timezone);
            } elseif (isset($sessionData['to'])) {
                $this->comparePeriodCache['to'] = $sessionData['to'];
            }
        }
        $this->session->setData(self::COMPARE_SESSION_KEY, $this->comparePeriodCache);

        return $this->comparePeriodCache;
    }

    /**
     * Resolve compare range
     *
     * @param string $periodType
     * @param string $comparePeriodType
     * @return array
     */
    private function resolveCompareRange($periodType, $comparePeriodType)
    {
        $comparePeriod = [];
        $periodRange = $this->periodRangeResolver->resolve($periodType);
        if ($comparePeriodType == CompareSource::TYPE_PREVIOUS_PERIOD) {
            $comparePeriod['from'] = $periodRange['c_from'];
            $comparePeriod['to'] = $periodRange['c_to'];
        } elseif ($comparePeriodType == CompareSource::TYPE_PREVIOUS_YEAR) {
            $comparePeriod['from'] = $periodRange['c_year_from'];
            $comparePeriod['to'] = $periodRange['c_year_to'];
        }

        return $comparePeriod;
    }

    /**
     * Retrieve current period
     *
     * @return array
     */
    protected function getPeriod()
    {
        if (!$this->isCacheUsed) {
            $this->periodCache = null;
        }
        if (null !== $this->periodCache) {
            return $this->periodCache;
        }

        $this->periodCache = [];
        $periodType = $this->getDefaultPeriodType();
        $sessionData = $this->session->getData(self::PERIOD_SESSION_KEY);

        $requestPeriodTypeParamValue = $this->request->getParam('period_type');
        if ($requestPeriodTypeParamValue !== null) {
            $periodType = $requestPeriodTypeParamValue;
        } else {
            if ($sessionData !== null) {
                $periodType = $sessionData['type'];
            }
        }

        $this->periodCache['type'] = $periodType;
        if ($periodType == PeriodSource::PERIOD_TYPE_CUSTOM) {
            $timezone = new \DateTimeZone($this->getLocaleTimezone());

            $requestFromParamValue = $this->request->getParam('period_from');
            if ($requestFromParamValue !== null) {
                $this->periodCache['from'] = new \DateTime($requestFromParamValue, $timezone);
            } elseif (isset($sessionData['from'])) {
                $this->periodCache['from'] = $sessionData['from'];
            }
            $requestToParamValue = $this->request->getParam('period_to');
            if ($requestToParamValue !== null) {
                $this->periodCache['to'] = new \DateTime($requestToParamValue, $timezone);
            } elseif (isset($sessionData['to'])) {
                $this->periodCache['to'] = $sessionData['to'];
            }
        } else {
            $this->periodCache = array_merge(
                $this->periodCache,
                $this->periodRangeResolver->resolve($this->periodCache['type'])
            );
        }
        $this->session->setData(self::PERIOD_SESSION_KEY, $this->periodCache);

        return $this->periodCache;
    }

    /**
     * Retrieve locale timezone
     *
     * @return string
     */
    private function getLocaleTimezone()
    {
        return $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
