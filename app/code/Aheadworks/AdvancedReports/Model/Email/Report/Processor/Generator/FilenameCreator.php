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

use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface;
use Aheadworks\AdvancedReports\Model\Source\Email\Report\Type as ReportTypeSource;
use Aheadworks\AdvancedReports\Model\Email\Report\PeriodResolver;

/**
 * Class FilenameCreator
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator
 */
class FilenameCreator
{
    /**
     * @var PeriodResolver
     */
    private $periodResolver;

    /**
     * @var ReportTypeSource
     */
    private $reportTypeSource;

    /**
     * @param PeriodResolver $periodResolver
     * @param ReportTypeSource $reportTypeSource
     */
    public function __construct(
        PeriodResolver $periodResolver,
        ReportTypeSource $reportTypeSource
    ) {
        $this->periodResolver = $periodResolver;
        $this->reportTypeSource = $reportTypeSource;
    }

    /**
     * Create file name depending on report
     *
     * @param string $reportType
     * @param ConfigInterface $reportConfig
     * @return string
     */
    public function create($reportType, ConfigInterface $reportConfig)
    {
        $reportName = $this->reportTypeSource->getReportLabel($reportType);
        list($from, $to) = $this->periodResolver->getPeriodsFormatted($reportConfig);

        return sprintf("%s: %s - %s.%s", $reportName, $from, $to, $reportConfig->getReportFormat());
    }
}
