<?php
/**
 * Class InstallSchema
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
namespace Winestop\Banner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup   setup
     * @param ModuleContextInterface $context context
     *
     * @return void
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'Winestop_banner'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('Winestop_banner')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Banner Id'
        )->addColumn(
            'banner_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Banner Title'
        )->addColumn(
            'banner_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Banner Description'
        )->addColumn(
            'banner_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,'default' => 'Image'],
            'Banner Type'
        )->addColumn(
            'banner_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Banner Image'
        )->addColumn(
            'banner_video',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Banner Video'
        )->addColumn(
            'banner_youtube',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Banner Youtube'
        )->addColumn(
            'banner_video_autoplay',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Banner Video Autoplay'
        )->addColumn(
            'start_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true,'default' => null],
            'Banner Start Date'
        )->addColumn(
            'end_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true,'default' => null],
            'Banner End Date'
        )->addColumn(
            'label_button_text',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Banner button text'
        )->addColumn(
            'call_to_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Banner Action'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Position'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Banner Active?'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Update Time'
        )->addIndex(
            $installer->getIdxName('Winestop_banner', ['banner_title']),
            ['banner_title']
        )->setComment('Winestop Banner');

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
