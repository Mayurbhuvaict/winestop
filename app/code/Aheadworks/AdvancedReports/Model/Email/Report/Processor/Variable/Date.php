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




























































namespace Aheadworks\AdvancedReports\Model\Email\Report\Processor\Variable;

use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface;
use Aheadworks\AdvancedReports\Model\Source\Email\Report\EmailVariables;
use Aheadworks\AdvancedReports\Model\Email\Report\PeriodResolver;

/**
 * Class Date
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor\Variable
 */
class Date implements VariableProcessorInterface
{
    /**
     * @var PeriodResolver
     */
    private $periodResolver;

    /**
     * @param PeriodResolver $periodResolver
     */
    public function __construct(
        PeriodResolver $periodResolver
    ) {
        $this->periodResolver = $periodResolver;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var ConfigInterface $ticket */
        $reportConfig = $variables[EmailVariables::REPORT_CONFIG];

        list($from, $to) = $this->periodResolver->getPeriodsFormatted($reportConfig);
        $variables[EmailVariables::REPORT_FROM_DATE] = $from;
        $variables[EmailVariables::REPORT_TO_DATE] = $to;

        return $variables;
    }
}
