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



namespace Mirasvit\SeoContent\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema103 implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable($installer->getTable(TemplateInterface::TABLE_NAME)),
            'apply_for_homepage',
            [
               'type'     => Table::TYPE_INTEGER,
               'length'   => 10,
               'unsigned' => true,
               'nullable' => false,
               'default'  => 0,
               'comment'  => 'is applicable for homepage',
            ]
        );
    }
}
