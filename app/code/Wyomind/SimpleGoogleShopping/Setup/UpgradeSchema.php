<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Setup;

/**
 * Upgrade schema for Simple Google Shopping
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * @var \Wyomind\Framework\Helper\History
     */
    protected $historyHelper;
    /**
     * @var \Wyomind\Framework\Helper\License\UpdateFactory
     */
    protected $license;

    /**
     * @param \Wyomind\Framework\Helper\History $historyHelper
     * @param \Wyomind\Framework\Helper\License\UpdateFactory $license
     */
    public function __construct(
        \Wyomind\Framework\Helper\History $historyHelper,
        \Wyomind\Framework\Helper\License\UpdateFactory $license
    ) {
    
        $this->license = $license;
        $this->historyHelper = $historyHelper;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
    
        $this->license->create()->update(__CLASS__, $context);
        $installer = $setup;
        $installer->startSetup();

        /**
         * upgrade to 14.0.0
         * Add simplegoogleshopping_name
         * Add simplegoogleshopping_note
         * Add simplegoogleshopping_history tables
         */
        if (version_compare($context->getVersion(), '14.0.0') < 0) {
            $tableName = 'simplegoogleshopping_feeds';

            if ($installer->tableExists($tableName)) {
                $connection = $installer->getConnection();
                $connection->addColumn(
                    $installer->getTable($tableName),
                    'simplegoogleshopping_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Data Feed Name'
                    ]
                );

                $connection->addColumn(
                    $installer->getTable($tableName),
                    'simplegoogleshopping_note',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Data Feed Note'
                    ]
                );
            }

            // Version history table - a line is added each time a feed is updated
            $this->historyHelper->createVersionHistoryTable($installer, $tableName);
            $this->historyHelper->createActionHistoryTable($installer, $tableName);
        }

        $installer->endSetup();
    }
}
