<?php
/**
 * FAQ setup file.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            /*
             $installer->getConnection()->addColumn(
                $installer->getTable('rootways_faq_category'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Store ID'
                ]
            );
            */
            
            $table = $installer->getConnection()->newTable(
                $installer->getTable('rootways_faq_cat_store')
            )->addColumn(
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false],
                'Category ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('rootways_faq_cat_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('rootways_faq_cat_store', 'cat_id', 'rootways_faq_category', 'cat_id'),
                'cat_id',
                $installer->getTable('rootways_faq_category'),
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('rootways_faq_cat_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'FAQ Category based on store'
            );
            $installer->getConnection()->createTable($table);
            
        }
        $installer->endSetup();
    }
}
