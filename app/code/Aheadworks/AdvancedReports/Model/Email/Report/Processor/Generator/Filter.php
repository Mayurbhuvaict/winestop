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
























































namespace Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator;

use Aheadworks\AdvancedReports\Model\Email\Report\PeriodResolver;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Filter
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator
 */
class Filter
{
    /**
     * @var PeriodResolver
     */
    private $periodResolver;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param PeriodResolver $periodResolver
     * @param RequestInterface $request
     */
    public function __construct(
        PeriodResolver $periodResolver,
        RequestInterface $request
    ) {
        $this->periodResolver = $periodResolver;
        $this->request = $request;
    }

    /**
     * Prepare filter params
     *
     * @param string $reportType
     * @param ConfigInterface $reportConfig
     */
    public function applyParams($reportType, ConfigInterface $reportConfig)
    {
        $namespace = 'aw_arep_' . $reportType . '_grid';
        list($from, $to) = $this->periodResolver->getPeriods($reportConfig);
        $params = [
            'namespace' => $namespace,
            'period_type' => $reportConfig->getWhenToSendFrequency(),
            'period_from' => $from->format('Y-m-d'),
            'period_to' => $to->format('Y-m-d'),
            'group_by' => $reportConfig->getReportGroupBy(),
            'compare_type' => 'disabled'
        ];
        $this->request->setParams($params);
    }
}
