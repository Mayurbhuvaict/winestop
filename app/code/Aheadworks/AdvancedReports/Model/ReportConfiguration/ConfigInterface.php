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



















































































































































































































namespace Aheadworks\AdvancedReports\Model\ReportConfiguration;

/**
 * Interface ConfigInterface
 *
 * @package Aheadworks\AdvancedReports\Model\ReportConfiguration
 */
interface ConfigInterface
{
    /**
     * Columns customization configuration
     */
    const COLUMNS_CUSTOMIZATION = 'columns_customization';

    /**
     * Get columns customization data
     *
     * @return \Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface[]
     */
    public function getColumnsCustomization();

    /**
     * Set columns customization data
     *
     * @param \Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface[] $columnsCustom
     * @return $this
     */
    public function setColumnsCustomization($columnsCustom);
}
