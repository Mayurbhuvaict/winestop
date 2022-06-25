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

/**
 * Interface ConfigInterface
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report
 */
interface ConfigInterface
{
    const REPORTS_TO_EXPORT = 'reports_to_export';
    const RECIPIENTS = 'recipients';
    const WHEN_TO_SEND_FREQUENCY = 'when_to_send_frequency';
    const REPORT_GROUP_BY = 'report_group_by';
    const REPORT_FORMAT = 'report_format';

    /**
     * Get reports to export
     *
     * @return array
     */
    public function getReportsToExport();

    /**
     * Set reports to export
     *
     * @param array $reportList
     * @return $this
     */
    public function setReportToExport($reportList);

    /**
     * Get recipients
     *
     * @return array
     */
    public function getRecipients();

    /**
     * Set recipients
     *
     * @param array $recipients
     * @return $this
     */
    public function setRecipients($recipients);

    /**
     * Get recipients
     *
     * @return string
     */
    public function getWhenToSendFrequency();

    /**
     * Set recipients
     *
     * @param string $frequency
     * @return $this
     */
    public function setWhenToSendFrequency($frequency);

    /**
     * Get report group by
     *
     * @return string
     */
    public function getReportGroupBy();

    /**
     * Set report group by
     *
     * @param string $reportGroupBy
     * @return $this
     */
    public function setReportGroupBy($reportGroupBy);

    /**
     * Get report format
     *
     * @return string
     */
    public function getReportFormat();

    /**
     * Set report format
     *
     * @param string $reportFormat
     * @return $this
     */
    public function setReportFormat($reportFormat);
}
