<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @version 2.0.0
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->dropTable($installer->getTable('watchlog')); // drop if exists

        $watchlog = $installer->getConnection()
            ->newTable($installer->getTable('watchlog'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true ],
                'ID'
            )
            ->addColumn(
                'ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                25,
                [ 'nullable' => false],
                'IP'
            )
            ->addColumn(
                'date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [ 'nullable' => false],
                'Attempt date'
            )
                
            ->addColumn(
                'login',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                [ 'nullable' => true],
                'Attempt login'
            )
            ->addColumn(
                'password',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                [ 'nullable' => true],
                'Attempt password (only stored if failure)'
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [ 'nullable' => true],
                'Attempt message'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'nullable' => false],
                'Attempt status'
            )
            ->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [ 'nullable' => true],
                'Attempt url'
            )
            ->addIndex(
                $installer->getIdxName('watchlog', ['id']),
                ['id']
            )
            ->setComment('Watchlog login attempts Table');

        $installer->getConnection()->createTable($watchlog);
        
        
        $installer->endSetup();
    }
}
