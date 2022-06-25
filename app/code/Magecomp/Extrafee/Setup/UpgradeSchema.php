<?php

namespace Magecomp\Extrafee\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    private $eavConfig;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';

        //Setup two columns for quote, quote_address and order
        //Quote address tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' =>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Fee'
                ]
            );

        //Quote tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Fee'

                ]
            );

        //Order tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Fee'

                ]
            );

        //Invoice tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Fee'

                ]
            );
        //Credit memo tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Fee'

                ]
            );


        if (version_compare($context->getVersion(), '1.0.5') <= 0)
        {

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteAddressTable),
                    'fee_tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' =>'10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Fee Tax'
                    ]
                );

            //Quote tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($quoteTable),
                    'fee_tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'=>'10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Fee Tax'

                    ]
                );

            //Order tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($orderTable),
                    'fee_tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'=>'10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Fee Tax'

                    ]
                );

            //Invoice tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($invoiceTable),
                    'fee_tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'=>'10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Fee Tax'

                    ]
                );
            //Credit memo tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($creditmemoTable),
                    'fee_tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'=>'10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Fee Tax'

                    ]
                );

        }

        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.6') <= 0)
        {
            $table1 = $installer->getConnection()->newTable($installer->getTable('shippingrate_category'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id Primary key'
                )
                ->addColumn(
                    'category_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Category name'
                )
                ->addColumn(
                    'is_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Is Active'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    12,
                    ['nullable' => false],
                    'Position'
                )
                ->addIndex('shippingrate_category', 'category_name');

                $installer->getConnection()->createTable($table1);

                
                $table = $installer->getTable( 'shippingmethod');
                if ($installer->tableExists($table)) {
                    $connection = $installer->getConnection();
                    if ($connection->tableColumnExists($table, 'category_id') === false) {
                        $connection->addColumn(
                            $table,
                            'category_id',
                            [
                                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                'nullable' => true,
                                'comment' => 'Category',
                                'default' => '1'
                            ]
                        );
                    }
                }
        }

        if (version_compare($context->getVersion(), '1.0.7', '<=')) {
            $eavSetup = $this->eavSetupFactory->create();
            $eavSetup->addAttribute('customer_address', 'type', [
                'type' => 'varchar',
                'input' => 'select',
                'label' => 'Address Type',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'visible_on_front' => true,
                'source' => \Magecomp\Extrafee\Model\Source\AddressType::class,
            ]);

            $customAttribute = $this->eavConfig->getAttribute('customer_address', 'type');

            $customAttribute->setData(
                'used_in_forms',
                [
                    'adminhtml_customer_address',
                    'adminhtml_checkout',
                    'customer_address_edit',
                    'customer_register_address'
                ]
            );
            $customAttribute->save();

            $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
                'type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    'length' => 255,
                    'comment' => 'Address Type'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_address'),
                'type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                    'length' => 255,
                    'comment' => 'Address Type'
                ]
            );
        }

        $setup->endSetup();
    }
}
