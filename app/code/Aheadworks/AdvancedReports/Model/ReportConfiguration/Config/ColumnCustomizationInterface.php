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




















































































































































































































namespace Aheadworks\AdvancedReports\Model\ReportConfiguration\Config;

/**
 * Interface ColumnCustomizationInterface
 *
 * @package Aheadworks\AdvancedReports\Model\ReportConfiguration\Config
 */
interface ColumnCustomizationInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const COLUMN_NAME = 'column_name';
    const CUSTOM_LABEL = 'custom_label';
    const IS_EXPORTED_TO_EMAIL = 'is_exported_to_email';
    /**#@-*/

    /**
     * Get column name
     *
     * @return string
     */
    public function getColumnName();

    /**
     * Set column name
     *
     * @param string $columnName
     * @return $this
     */
    public function setColumnName($columnName);

    /**
     * Get column custom label
     *
     * @return string
     */
    public function getCustomLabel();

    /**
     * Set column custom label
     *
     * @param string $customLabel
     * @return $this
     */
    public function setCustomLabel($customLabel);

    /**
     * Is column used in email
     *
     * @return string
     */
    public function isExportedToEmail();

    /**
     * Set is column used in email
     *
     * @param string $isExportedToEmail
     * @return $this
     */
    public function setIsExportedToEmail($isExportedToEmail);
}
