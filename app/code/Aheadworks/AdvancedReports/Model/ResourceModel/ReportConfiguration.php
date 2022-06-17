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









































































namespace Aheadworks\AdvancedReports\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReportConfiguration
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class ReportConfiguration extends AbstractDb
{
    /**#@+
     * Columns list
     */
    const ID = 'id';
    const REPORT_NAME = 'report_name';
    const REPORT_CONFIGURATION = 'configuration';
    /**#@-*/

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_report_configuration', self::ID);
    }

    /**
     * Save report configuration
     *
     * @param string $reportName
     * @param string $configuration
     * @throws \Exception
     * @return $this
     */
    public function saveConfiguration($reportName, $configuration)
    {
        $connection = $this->transactionManager->start($this->getConnection());
        try {
            $oldConfig = $this->loadConfiguration($reportName);
            $data = [
                self::REPORT_NAME => $reportName,
                self::REPORT_CONFIGURATION => $configuration
            ];
            if ($oldConfig) {
                $connection->update($this->getMainTable(), $data, [self::REPORT_NAME . ' = (?)' => $reportName]);
            } else {
                $connection->insert($this->getMainTable(), $data);
            }
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Load report configuration
     *
     * @param string $reportName
     * @return string|null
     * @throws LocalizedException
     */
    public function loadConfiguration($reportName)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [self::REPORT_CONFIGURATION])
            ->where(self::REPORT_NAME . ' =:report_name');
        $data = $this->getConnection()->fetchRow($select, [':report_name' => $reportName]);

        return $data && $data[self::REPORT_CONFIGURATION] ? $data[self::REPORT_CONFIGURATION] : null;
    }
}
