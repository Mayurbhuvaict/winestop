<?php
namespace Webkul\OutOfStockNotification\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /*
         * Create table 'form_data'
         */
        $table = $setup->getTable('wk_out_of_stock_product');
        $conn = $setup->getConnection();

        $conn->addColumn(
            $table,
            'website_code',
            [
            //Adding new column in table
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'=>225,
                'nullable' => false,
                
                'comment'=>'Website_code'
            ]
        );

        $setup->endSetup();
    }
}
