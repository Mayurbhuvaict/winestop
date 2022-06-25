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


namespace Mirasvit\SeoAudit\Setup\UpgradeSchema;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class UpgradeSchema101 implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();

        $connection->changeColumn(
            $setup->getTable(CheckResultInterface::TABLE_NAME),
            CheckResultInterface::URL_TYPE,
            CheckResultInterface::URL_TYPE,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'URI',
            ]
        );

        $connection->addIndex(
            $setup->getTable(CheckResultInterface::TABLE_NAME),
            $setup->getIdxName(
                CheckResultInterface::TABLE_NAME,
                [CheckResultInterface::URL_TYPE, CheckResultInterface::JOB_ID],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [
                CheckResultInterface::URL_TYPE,
                CheckResultInterface::JOB_ID
            ]
        );

        $connection->addIndex(
            $setup->getTable(CheckResultInterface::TABLE_NAME),
            $setup->getIdxName(
                CheckResultInterface::TABLE_NAME,
                [CheckResultInterface::URL_TYPE, CheckResultInterface::JOB_ID, CheckResultInterface::RESULT],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [
                CheckResultInterface::URL_TYPE,
                CheckResultInterface::JOB_ID,
                CheckResultInterface::RESULT
            ]
        );

        $connection->addIndex(
            $setup->getTable(UrlInterface::TABLE_NAME),
            $setup->getIdxName(
                UrlInterface::TABLE_NAME,
                [UrlInterface::STATUS],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [
                UrlInterface::STATUS
            ]
        );

        $connection->addIndex(
            $setup->getTable(UrlInterface::TABLE_NAME),
            $setup->getIdxName(
                UrlInterface::TABLE_NAME,
                [UrlInterface::JOB_ID, UrlInterface::STATUS_CODE],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [
                UrlInterface::JOB_ID,
                UrlInterface::STATUS_CODE
            ]
        );

        $connection->addIndex(
            $setup->getTable(UrlInterface::TABLE_NAME),
            $setup->getIdxName(
                UrlInterface::TABLE_NAME,
                [UrlInterface::JOB_ID, UrlInterface::STATUS_CODE, UrlInterface::TYPE],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            [
                UrlInterface::JOB_ID,
                UrlInterface::STATUS_CODE,
                UrlInterface::TYPE
            ]
        );
    }
}
