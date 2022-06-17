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




































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter\Dashboard;

use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model\Filter\Dashboard
 */
class Period extends \Aheadworks\AdvancedReports\Model\Filter\Period
{
    /**
     * @var array|null
     */
    private $periodRangeByRequest;

    /**
     * {@inheritdoc}
     */
    public function isThisMonthForecastEnabled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getComparePeriod()
    {
        $period = $this->getPeriodByRequest();

        return [
            'enabled' => true,
            'type' => $this->getDefaultCompareType(),
            'from' => $period['c_from'],
            'to' => $period['c_to'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPeriod()
    {
        return $this->getPeriodByRequest();
    }

    /**
     * Retrieve period by request
     *
     * @return array
     */
    private function getPeriodByRequest()
    {
        if (null === $this->periodRangeByRequest) {
            $periodType = $this->request->getParam('period_type');
            if (empty($periodType)) {
                $periodType = $this->getDefaultPeriodType();
            }
            $this->periodRangeByRequest = $this->periodRangeResolver->resolve($periodType);
            $this->periodRangeByRequest['type'] = $periodType;
        }

        return $this->periodRangeByRequest;
    }
}
