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

use Magento\Framework\DataObject;

/**
 * Class ColumnCustomization
 *
 * @package Aheadworks\AdvancedReports\Model\ReportConfiguration\Config
 */
class ColumnCustomization extends DataObject implements ColumnCustomizationInterface
{
    /**
     * @inheritdoc
     */
    public function getColumnName()
    {
        return $this->getData(self::COLUMN_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setColumnName($columnName)
    {
        return $this->setData(self::COLUMN_NAME, $columnName);
    }

    /**
     * @inheritdoc
     */
    public function getCustomLabel()
    {
        return $this->getData(self::CUSTOM_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomLabel($customLabel)
    {
        return $this->setData(self::CUSTOM_LABEL, $customLabel);
    }

    /**
     * @inheritdoc
     */
    public function isExportedToEmail()
    {
        return $this->getData(self::IS_EXPORTED_TO_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setIsExportedToEmail($isExportedToEmail)
    {
        return $this->setData(self::IS_EXPORTED_TO_EMAIL, $isExportedToEmail);
    }
}
