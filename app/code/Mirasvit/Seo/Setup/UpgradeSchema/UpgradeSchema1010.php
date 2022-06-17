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


namespace Mirasvit\Seo\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema1010 implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('mst_seo_template'),
                'category_description',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '64K',
                    'unsigned' => false,
                    'nullable' => true,
                    'comment' => 'Category Description',
                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable('mst_seo_template'),
                'category_image',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'unsigned' => false,
                    'nullable' => true,
                    'comment' => 'Category Image',
                ]
            );
    }
}