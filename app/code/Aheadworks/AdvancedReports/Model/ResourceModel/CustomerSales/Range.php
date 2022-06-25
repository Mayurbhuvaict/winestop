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






















































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales;

/**
 * Class Range
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales
 */
class Range extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_customer_sales_range', 'range_id');
    }

    /**
     * Save configuration values
     *
     * @param [] $configValue
     * @param int $websiteId
     * @throws \Exception
     * @return $this
     */
    public function saveConfigValue($configValue, $websiteId)
    {
        foreach ($configValue as &$row) {
            foreach ($row as &$value) {
                if ($value == '') {
                    $value = null;
                }
            }
            $row['website_id'] = $websiteId;
        }

        $connection = $this->transactionManager->start($this->getConnection());
        try {
            $connection->delete($this->getMainTable(), ['website_id = ?' => $websiteId]);
            $connection->insertOnDuplicate(
                $this->getMainTable(),
                $configValue,
                []
            );
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Load configuration values
     *
     * @param int $websiteId
     * @return []
     */
    public function loadConfigValue($websiteId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable() . '.website_id IN (?)', $websiteId)
            ->order($this->getMainTable() . '.range_from ' . Collection::SORT_ORDER_ASC)
        ;
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }

    /**
     * Remove configuration values
     *
     * @param int $websiteId
     * @return $this
     */
    public function removeConfigValue($websiteId)
    {
        $this->getConnection()->delete($this->getMainTable(), ['website_id = ?' => $websiteId]);
        return $this;
    }

    /**
     * If there are config values for specified store
     *
     * @param int $websiteId
     * @return bool
     */
    public function hasConfigValuesForWebsite($websiteId)
    {
        $data = $this->loadConfigValue($websiteId);
        return count($data) > 0;
    }
}
