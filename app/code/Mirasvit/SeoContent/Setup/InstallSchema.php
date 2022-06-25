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



namespace Mirasvit\SeoContent\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Magento\Config\Model\Config\Factory as ConfigFactory;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * InstallSchema constructor.
     * @param ConfigFactory $configFactory
     */
    public function __construct(
        ConfigFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $installer->startSetup();

        if ($connection->isTableExists($installer->getTable(TemplateInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(TemplateInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(TemplateInterface::TABLE_NAME)
        )->addColumn(
            'template_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            TemplateInterface::ID
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            TemplateInterface::NAME
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::IS_ACTIVE
        )->addColumn(
            'rule_type',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            TemplateInterface::RULE_TYPE
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            TemplateInterface::STORE_IDS
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            TemplateInterface::SORT_ORDER
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_TITLE
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_KEYWORDS
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_DESCRIPTION
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::TITLE
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::DESCRIPTION
        )->addColumn(
            'short_description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::SHORT_DESCRIPTION
        )->addColumn(
            'full_description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::FULL_DESCRIPTION
        )->addColumn(
            'description_position',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::DESCRIPTION_POSITION
        )->addColumn(
            'description_template',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::DESCRIPTION_TEMPLATE
        )->addColumn(
            'category_description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::CATEGORY_DESCRIPTION
        )->addColumn(
            'category_image',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::CATEGORY_IMAGE
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => false],
            TemplateInterface::CONDITIONS_SERIALIZED
        )->addColumn(
            'stop_rules_processing',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::STOP_RULE_PROCESSING
        )->addColumn(
            'apply_for_child_categories',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::APPLY_FOR_CHILD_CATEGORIES
        );
        $connection->createTable($table);

        if ($connection->isTableExists($installer->getTable(RewriteInterface::TABLE_NAME))) {
            $connection->dropTable($installer->getTable(RewriteInterface::TABLE_NAME));
        }

        $table = $connection->newTable(
            $installer->getTable(RewriteInterface::TABLE_NAME)
        )->addColumn(
            'rewrite_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            RewriteInterface::ID
        )->addColumn(
            'url',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            RewriteInterface::URL
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::IS_ACTIVE
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            TemplateInterface::STORE_IDS
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            TemplateInterface::SORT_ORDER
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_TITLE
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_KEYWORDS
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::META_DESCRIPTION
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::TITLE
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::DESCRIPTION
        )->addColumn(
            'description_position',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            TemplateInterface::DESCRIPTION_POSITION
        )->addColumn(
            'description_template',
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            TemplateInterface::DESCRIPTION_TEMPLATE
        );
        $connection->createTable($table);

        if ($connection->isTableExists($installer->getTable('mst_seo_template'))) {
            $rows = $connection->fetchAll($connection->select()->from($installer->getTable('mst_seo_template')));

            foreach ($rows as $row) {
                unset($row['actions_serialized']);
                $row['store_ids'] = '0';
                $connection->insert($installer->getTable(TemplateInterface::TABLE_NAME), $row);
            }

            $connection->dropTable($installer->getTable('mst_seo_template'));
            $connection->dropTable($installer->getTable('mst_seo_template_store'));
        }

        if ($connection->isTableExists($installer->getTable('mst_seo_rewrite'))) {
            $rows = $connection->fetchAll($connection->select()->from($installer->getTable('mst_seo_rewrite')));

            foreach ($rows as $row) {
                $row['store_ids'] = '0';
                $connection->insert($installer->getTable(RewriteInterface::TABLE_NAME), $row);
            }

            $connection->dropTable($installer->getTable('mst_seo_rewrite'));
            $connection->dropTable($installer->getTable('mst_seo_rewrite_store'));
        }

        $config = [
            'seo/general/is_category_meta_tags_used'
            => 'seo/seo_content/meta/is_category_meta_tags_used',

            'seo/general/is_product_meta_tags_used'
            => 'seo/seo_content/meta/is_product_meta_tags_used',

            'seo/extended/meta_title_page_number'
            => 'seo/seo_content/pagination/meta_title_page_number',

            'seo/extended/meta_description_page_number'
            => 'seo/seo_content/pagination/meta_description_page_number',

            'seo/general/is_use_html_symbols_in_meta_tags'
            => 'seo/seo_content/limiter/is_use_html_symbols_in_meta_tags',

            'seo/extended/meta_title_max_length'
            => 'seo/seo_content/limiter/meta_title_max_length',

            'seo/extended/meta_description_max_length'
            => 'seo/seo_content/limiter/meta_description_max_length',

            'seo/extended/product_name_max_length'
            => 'seo/seo_content/limiter/product_name_max_length',

            'seo/extended/product_short_description_max_length'
            => 'seo/seo_content/limiter/product_short_description_max_length',
        ];
        foreach ($config as $oldPath => $newPath) {
            $connection->update(
                $installer->getTable('core_config_data'),
                ['path' => $newPath],
                $connection->quoteInto('path = ?', $oldPath)
            );
        }

        $installer->endSetup();
    }
}
