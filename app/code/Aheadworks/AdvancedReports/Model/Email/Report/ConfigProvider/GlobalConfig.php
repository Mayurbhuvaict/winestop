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




















































namespace Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider;

use Aheadworks\AdvancedReports\Model\Config as ModuleConfig;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface as ReportConfigInterface;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterfaceFactory as ReportConfigInterfaceFactory;

/**
 * Class GlobalConfig
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider
 */
class GlobalConfig
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ReportConfigInterfaceFactory
     */
    private $reportConfigFactory;

    /**
     * @param ModuleConfig $moduleConfig
     * @param ReportConfigInterfaceFactory $reportConfigFactory
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        ReportConfigInterfaceFactory $reportConfigFactory
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->reportConfigFactory = $reportConfigFactory;
    }

    /**
     * Get global report email config
     *
     * @return ReportConfigInterface
     */
    public function getConfig()
    {
        /** @var ReportConfigInterface $config */
        $config = $this->reportConfigFactory->create();
        $config
            ->setReportToExport($this->moduleConfig->getReportTypesToExport())
            ->setRecipients($this->moduleConfig->getEmailRecipients())
            ->setWhenToSendFrequency($this->moduleConfig->getWhenToSendFrequency())
            ->setReportGroupBy($this->moduleConfig->getReportGroupBy())
            ->setReportFormat($this->moduleConfig->getReportFormat());

        return $config;
    }
}
