<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $installer->getConnection();

        $installer->startSetup();

        if ($connection->isTableExists($installer->getTable(JobInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(JobInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(JobInterface::TABLE_NAME)
        )->addColumn(
            JobInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            JobInterface::ID
        )->addColumn(
            JobInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            JobInterface::STATUS
        )->addColumn(
            JobInterface::MESSAGE,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            JobInterface::MESSAGE
        )->addColumn(
            JobInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            JobInterface::CREATED_AT
        )->addColumn(
            JobInterface::STARTED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => null],
            JobInterface::STARTED_AT
        )->addColumn(
            JobInterface::FINISHED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => null],
            JobInterface::FINISHED_AT
        )->addColumn(
            JobInterface::RESULT_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            JobInterface::RESULT_SERIALIZED
        );

        $connection->createTable($table);

        if ($connection->isTableExists($installer->getTable(CheckResultInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(CheckResultInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(CheckResultInterface::TABLE_NAME)
        )->addColumn(
            CheckResultInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            CheckResultInterface::ID
        )->addColumn(
            CheckResultInterface::JOB_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            CheckResultInterface::JOB_ID
        )->addColumn(
            CheckResultInterface::URL_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            CheckResultInterface::URL_ID
        )->addColumn(
            CheckResultInterface::URL_TYPE,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            CheckResultInterface::URL_TYPE
        )->addColumn(
            CheckResultInterface::IDENTIFIER,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            CheckResultInterface::IDENTIFIER
        )->addColumn(
            CheckResultInterface::IMPORTANCE,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            CheckResultInterface::IMPORTANCE
        )->addColumn(
            CheckResultInterface::RESULT,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            CheckResultInterface::RESULT
        )->addColumn(
            CheckResultInterface::VALUE,
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            CheckResultInterface::VALUE
        )->addColumn(
            CheckResultInterface::MESSAGE,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            CheckResultInterface::MESSAGE
        )->addColumn(
            CheckResultInterface::CREATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            CheckResultInterface::CREATED_AT
        )->addIndex(
            $installer->getIdxName(CheckResultInterface::TABLE_NAME, [CheckResultInterface::URL_ID, CheckResultInterface::JOB_ID]),
            [CheckResultInterface::URL_ID, CheckResultInterface::JOB_ID]
        );

        $connection->createTable($table);

        if ($connection->isTableExists($installer->getTable(UrlInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(UrlInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(UrlInterface::TABLE_NAME)
        )->addColumn(
            UrlInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            UrlInterface::ID
        )->addColumn(
            UrlInterface::PARENT_IDS,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            UrlInterface::PARENT_IDS
        )->addColumn(
            UrlInterface::JOB_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            UrlInterface::JOB_ID
        )->addColumn(
            UrlInterface::URL,
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            UrlInterface::URL
        )->addColumn(
            UrlInterface::URL_HASH,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            UrlInterface::URL_HASH
        )->addColumn(
            UrlInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            UrlInterface::TYPE
        )->addColumn(
            UrlInterface::STATUS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            UrlInterface::STATUS
        )->addColumn(
            UrlInterface::STATUS_CODE,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true, 'default' => null],
            UrlInterface::STATUS_CODE
        )->addColumn(
            UrlInterface::META_TITLE,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            UrlInterface::META_TITLE
        )->addColumn(
            UrlInterface::META_DESCRIPTION,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            UrlInterface::META_DESCRIPTION
        )->addColumn(
            UrlInterface::ROBOTS,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            UrlInterface::ROBOTS
        )->addColumn(
            UrlInterface::CANONICAL,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            UrlInterface::CANONICAL
        )->addColumn(
            UrlInterface::CONTENT,
            Table::TYPE_TEXT,
            '256K',
            ['nullable' => true],
            UrlInterface::CONTENT
        )->addIndex(
            $installer->getIdxName(UrlInterface::TABLE_NAME, [UrlInterface::TYPE]),
            [UrlInterface::TYPE]
        )->addIndex(
            $installer->getIdxName(UrlInterface::TABLE_NAME, [UrlInterface::URL_HASH]),
            [UrlInterface::URL_HASH]
        )->addIndex(
            $installer->getIdxName(UrlInterface::TABLE_NAME, [UrlInterface::JOB_ID]),
            [UrlInterface::JOB_ID]
        );

        $connection->createTable($table);

        $installer->endSetup();
    }
}
