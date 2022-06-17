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

use Magento\Framework\DataObject;

/**
 * Class Config
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report
 */
class Config extends DataObject implements ConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsToExport()
    {
        return $this->getData(self::REPORTS_TO_EXPORT);
    }

    /**
     * @inheritdoc
     */
    public function setReportToExport($reportList)
    {
        return $this->setData(self::REPORTS_TO_EXPORT, $reportList);
    }

    /**
     * @inheritdoc
     */
    public function getRecipients()
    {
        return $this->getData(self::RECIPIENTS);
    }

    /**
     * @inheritdoc
     */
    public function setRecipients($recipients)
    {
        return $this->setData(self::RECIPIENTS, $recipients);
    }

    /**
     * @inheritdoc
     */
    public function getWhenToSendFrequency()
    {
        return $this->getData(self::WHEN_TO_SEND_FREQUENCY);
    }

    /**
     * @inheritdoc
     */
    public function setWhenToSendFrequency($frequency)
    {
        return $this->setData(self::WHEN_TO_SEND_FREQUENCY, $frequency);
    }

    /**
     * @inheritdoc
     */
    public function getReportGroupBy()
    {
        return $this->getData(self::REPORT_GROUP_BY);
    }

    /**
     * @inheritdoc
     */
    public function setReportGroupBy($reportGroupBy)
    {
        return $this->setData(self::REPORT_GROUP_BY, $reportGroupBy);
    }

    /**
     * @inheritdoc
     */
    public function getReportFormat()
    {
        return $this->getData(self::REPORT_FORMAT);
    }

    /**
     * @inheritdoc
     */
    public function setReportFormat($reportFormat)
    {
        return $this->setData(self::REPORT_FORMAT, $reportFormat);
    }
}
