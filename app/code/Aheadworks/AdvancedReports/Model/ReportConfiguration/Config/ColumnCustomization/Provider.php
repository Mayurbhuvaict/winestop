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






















































































































































































































namespace Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomization;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReports\Model\Service\ReportConfigurationService;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Provider
 *
 * @package Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomization
 */
class Provider
{
    /**
     * @var ReportConfigurationService
     */
    private $reportConfigurationService;

    /**
     * @param ReportConfigurationService $reportConfigurationService
     */
    public function __construct(
        ReportConfigurationService $reportConfigurationService
    ) {
        $this->reportConfigurationService = $reportConfigurationService;
    }

    /**
     * Get columns list for report
     *
     * @param string $reportName
     * @return ColumnCustomizationInterface[]
     * @throws LocalizedException
     */
    public function getColumnListForReport($reportName)
    {
        $columnsCustomization = [];
        $reportConfiguration = $this->reportConfigurationService->getReportConfiguration($reportName);
        if ($reportConfiguration && is_array($reportConfiguration->getColumnsCustomization())) {
            foreach ($reportConfiguration->getColumnsCustomization() as $columnCustomization) {
                $columnsCustomization[$columnCustomization->getColumnName()] = $columnCustomization;
            }
        }

        return $columnsCustomization;
    }

    /**
     * Get columns list for component
     *
     * @param UiComponentInterface $component
     * @return ColumnCustomizationInterface[]
     * @throws LocalizedException
     */
    public function getColumnListForComponent(UiComponentInterface $component)
    {
        $reportName = preg_replace('/aw_arep_|_grid/', '', $component->getName());
        return $this->getColumnListForReport($reportName);
    }
}
