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





























































































































































































































namespace Aheadworks\AdvancedReports\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\AdvancedReports\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'aw_arep_days'
         */
        $daysTable = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_days'))
            ->addColumn(
                'date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false]
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_days'), ['date']),
                ['date'],
                ['type' => 'unique']
            );
        $installer->getConnection()->createTable($daysTable);

        /**
         * Create table 'aw_arep_weeks'
         */
        $weeksTable = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_weeks'))
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'First day of week'
            )->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'End day of Week'
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_weeks'), ['start_date']),
                ['start_date'],
                ['type' => 'unique']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_weeks'), ['end_date']),
                ['end_date'],
                ['type' => 'unique']
            );
        $installer->getConnection()->createTable($weeksTable);

        /**
         * Create table 'aw_arep_month'
         */
        $monthTable = $installer->getConnection()->newTable($installer->getTable('aw_arep_month'))
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'First day of month'
            )->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'End day of month'
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_month'), ['start_date']),
                ['start_date'],
                ['type' => 'unique']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_month'), ['end_date']),
                ['end_date'],
                ['type' => 'unique']
            );
        $installer->getConnection()->createTable($monthTable);

        /**
         * Create table 'aw_arep_quarter'
         */
        $quarterTable = $installer->getConnection()->newTable($installer->getTable('aw_arep_quarter'))
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'First day of quarter'
            )->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'End day of quarter'
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_quarter'), ['start_date']),
                ['start_date'],
                ['type' => 'unique']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_quarter'), ['end_date']),
                ['end_date'],
                ['type' => 'unique']
            );
        $installer->getConnection()->createTable($quarterTable);

        /**
         * Create table 'aw_arep_year'
         */
        $yearTable = $installer->getConnection()->newTable($installer->getTable('aw_arep_year'))
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'First day of year'
            )->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'End day of year'
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_year'), ['start_date']),
                ['start_date'],
                ['type' => 'unique']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_year'), ['end_date']),
                ['end_date'],
                ['type' => 'unique']
            );
        $installer->getConnection()->createTable($yearTable);

        $installer->endSetup();
    }
}
