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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\AdvancedReports\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->addIndexTables($setup);
        }

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->addFieldToProductPerformanceIdxTables($setup);
            $this->addProductAttributeTables($setup);
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->addTrafficAndConversionsIndexTables($setup);
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $this->addCustomerGroupToProductViewLog($setup);
            $this->addCustomerGroupToIndexes($setup);
        }

        if (version_compare($context->getVersion(), '2.4.0', '<')) {
            $this->addSalesByLocationIndexes($setup);
            $this->addAbandonedCartsIndexes($setup);
        }

        if (version_compare($context->getVersion(), '2.5.0', '<')) {
            $this->addCustomerSalesIndexes($setup);
        }

        if (version_compare($context->getVersion(), '2.7.0', '<')) {
            $this->addPointsCreditColumn($setup);
        }

        if (version_compare($context->getVersion(), '2.8.0', '<')) {
            $this->addReportConfigurationTable($setup);
            $this->addExtraColumnsToSalesDetailedIndexTable($setup);
        }
    }

    /**
     * Add index tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addIndexTables(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_sales_overview'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_overview'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_sales_overview', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_sales_overview', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_sales_overview', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Sales Overview');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_sales_overview_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_overview_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Sales Overview Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_sales_overview_coupon_code'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_overview_coupon_code'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_sales_overview_coupon_code', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_sales_overview_coupon_code', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_sales_overview_coupon_code', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Sales Overview By Coupon Code ');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_sales_overview_coupon_code_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_overview_coupon_code_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Sales Overview By Coupon Code Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_performance', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Performance By Payment Type');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Performance By Payment Type Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_category'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_category'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance_category', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance_category', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_performance_category', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Performance By Category');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_category_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_category_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Performance By Category Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_coupon_code'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_coupon_code'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName(
                    'aw_arep_product_performance_coupon_code',
                    ['period', 'store_id', 'order_status']
                ),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance_coupon_code', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_performance_coupon_code', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Performance By Coupon Code');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_coupon_code_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_coupon_code_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Performance By Coupon Code Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_manufacturer'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_manufacturer'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'primary' => true],
                'Manufacturer'
            )->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName(
                    'aw_arep_product_performance_manufacturer',
                    ['period', 'store_id', 'order_status']
                ),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_performance_manufacturer', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_performance_manufacturer', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Performance By Manufacturer');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_performance_manufacturer_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_performance_manufacturer_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'primary' => true],
                'Manufacturer'
            )->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Performance By Manufacturer Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_variant_performance'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_variant_performance'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Id'
            )->addColumn(
                'parent_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Product Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_product_variant_performance', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_variant_performance', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_variant_performance', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Variant Performance');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_variant_performance_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_variant_performance_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Id'
            )->addColumn(
                'parent_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Parent Product Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Variant Performance Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_coupon_code'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_coupon_code'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_coupon_code', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_coupon_code', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_coupon_code', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Coupon Code');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_coupon_code_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_coupon_code_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Coupon Code'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Coupon Code Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_payment_type'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_payment_type'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'additional_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Payment Method Additional Information'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_payment_type', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_payment_type', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_payment_type', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Payment Type');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_payment_type_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_payment_type_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false, 'primary' => true],
                'Payment Method'
            )->addColumn(
                'additional_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Payment Method Additional Information'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Payment Type Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_manufacturer'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_manufacturer'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'primary' => true],
                'Manufacturer'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_manufacturer', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_manufacturer', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_manufacturer', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Manufacturer');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_manufacturer_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_manufacturer_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'primary' => true],
                'Manufacturer'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Manufacturer Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_category'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_category'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_category', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_category', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_category', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Category');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_category_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_category_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Category Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_sales_detailed'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_detailed'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Order Id'
            )->addColumn(
                'order_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Order Increment Id'
            )->addColumn(
                'order_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Order Date'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Manufacturer'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer Id'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'customer_group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Customer Group'
            )->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region'
            )->addColumn(
                'city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'City'
            )->addColumn(
                'zip_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Zip Code'
            )->addColumn(
                'address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Address'
            )->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Phone'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Coupon Code'
            )->addColumn(
                'qty_ordered',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Ordered'
            )->addColumn(
                'qty_invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Invoiced'
            )->addColumn(
                'qty_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Shipped'
            )->addColumn(
                'qty_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Refunded'
            )->addColumn(
                'item_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Item Price'
            )->addColumn(
                'item_cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Item Cost'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'total_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total Incl. Tax'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'tax_invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax Invoiced'
            )->addColumn(
                'invoiced_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced Incl. Tax'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'tax_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax Refunded'
            )->addColumn(
                'refunded_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded Incl. Tax'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_category', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_sales_detailed', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_sales_detailed', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Sales Detailed');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_sales_detailed_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_sales_detailed_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Order Id'
            )->addColumn(
                'order_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Order Increment Id'
            )->addColumn(
                'order_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Order Date'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'SKU'
            )->addColumn(
                'manufacturer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Manufacturer'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Customer Id'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'customer_group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Customer Group'
            )->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region'
            )->addColumn(
                'city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'City'
            )->addColumn(
                'zip_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Zip Code'
            )->addColumn(
                'address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Address'
            )->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Phone'
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Coupon Code'
            )->addColumn(
                'qty_ordered',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Ordered'
            )->addColumn(
                'qty_invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Invoiced'
            )->addColumn(
                'qty_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Shipped'
            )->addColumn(
                'qty_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Refunded'
            )->addColumn(
                'item_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Item Price'
            )->addColumn(
                'item_cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Item Cost'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'total_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total Incl. Tax'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'tax_invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax Invoiced'
            )->addColumn(
                'invoiced_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced Incl. Tax'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'tax_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax Refunded'
            )->addColumn(
                'refunded_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded Incl. Tax'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Sales Detailed Indexer Idx');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add fields to ProductPerformance tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addFieldToProductPerformanceIdxTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $productPerformanceIdxTables = [
            'aw_arep_product_performance',
            'aw_arep_product_performance_idx',
            'aw_arep_product_performance_category',
            'aw_arep_product_performance_category_idx',
            'aw_arep_product_performance_coupon_code',
            'aw_arep_product_performance_coupon_code_idx',
            'aw_arep_product_performance_manufacturer',
            'aw_arep_product_performance_manufacturer_idx'
        ];

        foreach ($productPerformanceIdxTables as $tableName) {
            $connection->addColumn(
                $installer->getTable($tableName),
                'total_revenue_excl_tax',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => false,
                    'comment'  => 'Total Revenue (excl. Tax)'
                ]
            );
            $connection->addColumn(
                $installer->getTable($tableName),
                'total_cost',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => false,
                    'comment'  => 'Total Cost'
                ]
            );
        }
    }

    /**
     * Add tables for Product Attributes report
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addProductAttributeTables(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_product_attributes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_attributes'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_product_attributes', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_product_attributes', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_product_attributes', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product Attributes');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_product_attributes_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_product_attributes_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Product Attributes Idx');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add traffic and conversions index tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addTrafficAndConversionsIndexTables(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_conversion'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_conversion'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true, 'default' => 'viewed'],
                'Order Status'
            )->addColumn(
                'views_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Views Count'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Orders Count'
            )->addIndex(
                $installer->getIdxName('aw_arep_conversion', ['period', 'store_id']),
                ['period', 'store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_conversion', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_conversion', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Traffic and Conversions');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_conversion_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_conversion_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true, 'default' => 'viewed'],
                'Order Status'
            )->addColumn(
                'views_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Views Count'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Orders Count'
            )->setComment('Arep Traffic and Conversions Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_conversion_product'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_conversion_product'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'views_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Views Count'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'is_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Is Refunded'
            )->addIndex(
                $installer->getIdxName('aw_arep_conversion_product', ['period', 'store_id']),
                ['period', 'store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_conversion_product', ['is_refunded']),
                ['is_refunded']
            )->addIndex(
                $installer->getIdxName('aw_arep_conversion_product', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_conversion_product', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Traffic and Conversions Product');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_conversion_product_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_conversion_product_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'views_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Views Count'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'is_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Is Refunded'
            )->setComment('Arep Traffic and Conversions Product Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_log_product_view'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_log_product_view'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Log Id'
            )->addColumn(
                'logged_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Logged At'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )->addColumn(
                'visitor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Visitor ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addIndex(
                $installer->getIdxName('aw_arep_log_product_view', ['visitor_id']),
                ['visitor_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_log_product_view', ['product_id']),
                ['product_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_log_product_view', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_log_product_view', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Product View Log Table');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add customer group id field to product view log table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCustomerGroupToProductViewLog(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $customerIdColumnType = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'nullable' => true,
            'unsigned' => true,
            'comment' => 'Customer Id',
            'after' => 'visitor_id'
        ];

        $customerGroupColumnType = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'nullable' => false,
            'unsigned' => true,
            'comment' => 'Customer Group Id',
            'after' => 'customer_id'
        ];

        $connection->addColumn(
            $installer->getTable('aw_arep_log_product_view'),
            'customer_id',
            $customerIdColumnType
        );
        $connection->addIndex(
            $installer->getTable('aw_arep_log_product_view'),
            $installer->getIdxName('aw_arep_log_product_view', ['customer_id']),
            ['customer_id']
        );
        $connection->addColumn(
            $installer->getTable('aw_arep_log_product_view'),
            'customer_group_id',
            $customerGroupColumnType
        );
        $connection->addIndex(
            $installer->getTable('aw_arep_log_product_view'),
            $installer->getIdxName('aw_arep_log_product_view', ['customer_group_id']),
            ['customer_group_id']
        );

        return $this;
    }

    /**
     * Add customer group field to index tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addCustomerGroupToIndexes(SchemaSetupInterface $installer)
    {
        $tablesToUpdate = [
            'aw_arep_sales_overview',
            'aw_arep_sales_overview_coupon_code',
            'aw_arep_product_performance',
            'aw_arep_product_performance_category',
            'aw_arep_product_performance_coupon_code',
            'aw_arep_product_performance_manufacturer',
            'aw_arep_product_variant_performance',
            'aw_arep_sales_detailed',
            'aw_arep_conversion',
            'aw_arep_conversion_product',
            'aw_arep_category',
            'aw_arep_product_attributes',
            'aw_arep_coupon_code',
            'aw_arep_payment_type',
            'aw_arep_manufacturer'
        ];

        foreach ($tablesToUpdate as $tableName) {
            $this->addGroupFieldToTable($installer, $tableName);
        }

        return $this;
    }

    /**
     * Add customer group id field to index table
     *
     * @param SchemaSetupInterface $installer
     * @param string $tableName
     * @return $this
     */
    private function addGroupFieldToTable(SchemaSetupInterface $installer, $tableName)
    {
        $connection = $installer->getConnection();
        $customerGroupColumnType = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'nullable' => false,
            'unsigned' => true,
            'comment' => 'Customer Group Id',
            'after' => 'order_status'
        ];

        $connection->addColumn(
            $installer->getTable($tableName),
            'customer_group_id',
            $customerGroupColumnType
        );
        $connection->addColumn(
            $installer->getTable($tableName . '_idx'),
            'customer_group_id',
            $customerGroupColumnType
        );
        $connection->addIndex(
            $installer->getTable($tableName),
            $installer->getIdxName($tableName, ['customer_group_id']),
            ['customer_group_id']
        );

        return $this;
    }

    /**
     * Add sales by location index tables
     *
     * @param SchemaSetupInterface $installer
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addSalesByLocationIndexes(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_location'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_location'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'country_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2,
                [],
                'Country Id'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Region'
            )->addColumn(
                'city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'City'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['store_id']),
                ['store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['customer_group_id']),
                ['customer_group_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['country_id']),
                ['country_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['region']),
                ['region']
            )->addIndex(
                $installer->getIdxName('aw_arep_location', ['city']),
                ['city']
            )->addForeignKey(
                $installer->getFkName('aw_arep_location', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Sales By Location');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_location_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_location_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'country_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2,
                [],
                'Country Id'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Region'
            )->addColumn(
                'city',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'City'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'order_items_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order Items Count'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Subtotal'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Tax'
            )->addColumn(
                'shipping',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Shipping'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Discounts'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'invoiced',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Invoiced'
            )->addColumn(
                'refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Sales By Location Indexer Idx');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add abandoned carts index tables
     *
     * @param SchemaSetupInterface $installer
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addAbandonedCartsIndexes(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_abandoned_carts'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_abandoned_carts'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'total_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Total Carts'
            )->addColumn(
                'completed_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Completed Carts'
            )->addColumn(
                'abandoned_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Abandoned Carts'
            )->addColumn(
                'abandoned_carts_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Abandoned Carts Total'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_abandoned_carts', ['period', 'store_id']),
                ['period', 'store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_abandoned_carts', ['store_id']),
                ['store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_abandoned_carts', ['customer_group_id']),
                ['customer_group_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_abandoned_carts', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Abandoned Carts');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_abandoned_carts_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_abandoned_carts_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'total_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Total Carts'
            )->addColumn(
                'completed_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Completed Carts'
            )->addColumn(
                'abandoned_carts',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Abandoned Carts'
            )->addColumn(
                'abandoned_carts_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Abandoned Carts Total'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Abandoned Carts Indexer Idx');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add customer sales index tables
     *
     * @param SchemaSetupInterface $installer
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addCustomerSalesIndexes(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_customer_sales'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_customer_sales'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Customer Id'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'customer_group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Customer Group'
            )->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region'
            )->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Phone'
            )->addColumn(
                'created_in',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Created From'
            )->addColumn(
                'last_login_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Last Login Time'
            )->addColumn(
                'last_order_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Last Order Time'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'qty_ordered',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Ordered'
            )->addColumn(
                'qty_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Refunded'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'total_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->addIndex(
                $installer->getIdxName('aw_arep_customer_sales', ['period', 'store_id', 'order_status']),
                ['period', 'store_id', 'order_status']
            )->addIndex(
                $installer->getIdxName('aw_arep_customer_sales', ['store_id']),
                ['store_id']
            )->addIndex(
                $installer->getIdxName('aw_arep_customer_sales', ['customer_group_id']),
                ['customer_group_id']
            )->addForeignKey(
                $installer->getFkName('aw_arep_customer_sales', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Customer Sales');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_customer_sales_idx'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_customer_sales_idx'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false, 'primary' => true],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false, 'primary' => true],
                'Order Status'
            )->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Customer Group Id'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Customer Id'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Name'
            )->addColumn(
                'customer_group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => false],
                'Customer Group'
            )->addColumn(
                'country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            )->addColumn(
                'region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region'
            )->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Phone'
            )->addColumn(
                'created_in',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Created From'
            )->addColumn(
                'last_login_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Last Login Time'
            )->addColumn(
                'last_order_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Last Order Time'
            )->addColumn(
                'orders_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Orders Count'
            )->addColumn(
                'qty_ordered',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Ordered'
            )->addColumn(
                'qty_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty. Refunded'
            )->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total'
            )->addColumn(
                'total_refunded',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Total Refunded'
            )->addColumn(
                'to_global_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'To Global Rate'
            )->setComment('Arep Customer Sales Indexer Idx');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_arep_customer_sales_range'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_customer_sales_range'))
            ->addColumn(
                'range_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Range Id'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )->addColumn(
                'range_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => false],
                'Range From'
            )->addColumn(
                'range_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,2',
                ['nullable' => true],
                'Range To'
            )->addIndex(
                $installer->getIdxName('aw_arep_customer_sales_range', ['website_id']),
                ['website_id']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_customer_sales_range'), ['range_from']),
                ['range_from']
            )->addIndex(
                $installer->getIdxName($installer->getTable('aw_arep_customer_sales_range'), ['range_to']),
                ['range_to']
            )->addForeignKey(
                $installer->getFkName('aw_arep_customer_sales_range', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $installer->getTable('store_website'),
                'website_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Arep Customer Sales Range');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add pints and credit column to index table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addPointsCreditColumn(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $orderBasedIdxTables = [
            'aw_arep_sales_overview',
            'aw_arep_sales_overview_idx',
            'aw_arep_sales_overview_coupon_code',
            'aw_arep_sales_overview_coupon_code_idx',
            'aw_arep_location',
            'aw_arep_location_idx',
            'aw_arep_coupon_code',
            'aw_arep_coupon_code_idx',
            'aw_arep_payment_type',
            'aw_arep_payment_type_idx',
        ];

        foreach ($orderBasedIdxTables as $table) {
            $connection->addColumn(
                $installer->getTable($table),
                'other_discount',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'    => '12,4',
                    'nullable'  => false,
                    'after'     => 'discount',
                    'comment'   => 'Other Discounts'
                ]
            );
        }

        return $this;
    }

    /**
     * Add report configuration table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addReportConfigurationTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'aw_arep_report_configuration'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_arep_report_configuration'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'report_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Report Name'
            )->addColumn(
                'configuration',
                Table::TYPE_TEXT,
                Table::MAX_TEXT_SIZE,
                ['nullable' => true],
                'Report Configuration'
            )->setComment('Report Configuration table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Update columns in sales details index table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addExtraColumnsToSalesDetailedIndexTable($setup)
    {
        $tableNames = ['aw_arep_sales_detailed', 'aw_arep_sales_detailed_idx'];
        foreach ($tableNames as $tableName) {
            $this->addColumnsToTable(
                $setup,
                [
                    [
                        'fieldName' => 'base_grand_total',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'nullable' => false,
                            'comment' => 'Grand Total (Base)'
                        ]
                    ],
                    [
                        'fieldName' => 'shipping_information',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Shipping Information'
                        ]
                    ],
                    [
                        'fieldName' => 'shipping_amount',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'nullable' => false,
                            'comment' => 'Shipping Amount'
                        ]
                    ],
                    [
                        'fieldName' => 'order_modified_date',
                        'config' => [
                            'type' => Table::TYPE_TIMESTAMP,
                            'length' => null,
                            'nullable' => false,
                            'comment' => 'Order Modified Date'
                        ]
                    ],
                    [
                        'fieldName' => 'tax_code',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '100',
                            'nullable' => false,
                            'comment' => 'Tax code'
                        ]
                    ],
                    [
                        'fieldName' => 'tax_percent',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'nullable' => false,
                            'comment' => 'Tax Percent'
                        ]
                    ],
                    [
                        'fieldName' => 'base_tax_real_amount',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'nullable' => false,
                            'comment' => 'Tax Real Amount (Base)'
                        ]
                    ],
                    [
                        'fieldName' => 'billing_region',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Billing Region'
                        ]
                    ],
                    [
                        'fieldName' => 'billing_zip_code',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Billing Postcode'
                        ]
                    ],
                    [
                        'fieldName' => 'billing_city',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Billing City'
                        ]
                    ],
                    [
                        'fieldName' => 'customer_balance_refunded',
                        'config' => [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'nullable' => false,
                            'comment' => 'Customer Balance Refunded'
                        ]
                    ]
                ],
                $tableName
            );

            $this->renameColumnsInTable(
                $setup,
                [
                    [
                        'oldFieldName' => 'region',
                        'newFieldName' => 'shipping_region',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Shipping Region'
                        ]
                    ],
                    [
                        'oldFieldName' => 'zip_code',
                        'newFieldName' => 'shipping_zip_code',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Shipping Postcode'
                        ]
                    ],
                    [
                        'oldFieldName' => 'city',
                        'newFieldName' => 'shipping_city',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'length' => '255',
                            'nullable' => false,
                            'comment' => 'Shipping City'
                        ]
                    ],
                ],
                $tableName
            );
        }

        return $this;
    }

    /**
     * Add columns to table
     *
     * @param SchemaSetupInterface $setup
     * @param array $columnsConfig
     * @param string $tableName
     * @return $this
     */
    private function addColumnsToTable($setup, $columnsConfig, $tableName)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);
        foreach ($columnsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['fieldName'];
            if ($connection->tableColumnExists($tableName, $fieldName)) {
                continue;
            }
            $connection->addColumn(
                $tableName,
                $fieldName,
                $fieldConfig['config']
            );
        }

        return $this;
    }

    /**
     * Rename columns in table
     *
     * @param SchemaSetupInterface $setup
     * @param array $columnsConfig
     * @param string $tableName
     * @return $this
     */
    private function renameColumnsInTable($setup, $columnsConfig, $tableName)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);
        foreach ($columnsConfig as $fieldConfig) {
            $oldFieldName = $fieldConfig['oldFieldName'];
            if (!$connection->tableColumnExists($tableName, $oldFieldName)) {
                continue;
            }
            $connection->changeColumn(
                $tableName,
                $oldFieldName,
                $fieldConfig['newFieldName'],
                $fieldConfig['config']
            );
        }

        return $this;
    }
}
