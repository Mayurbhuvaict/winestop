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



namespace Mirasvit\Seo\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchemaInterface[]
     */
    private $pool;

    /**
     * UpgradeSchema constructor.
     * @param UpgradeSchema\UpgradeSchema105 $upgrade105
     * @param UpgradeSchema\UpgradeSchema106 $upgrade106
     * @param UpgradeSchema\UpgradeSchema107 $upgrade107
     * @param UpgradeSchema\UpgradeSchema108 $upgrade108
     * @param UpgradeSchema\UpgradeSchema109 $upgrade109
     * @param UpgradeSchema\UpgradeSchema1010 $upgrade1010
     * @param UpgradeSchema\UpgradeSchema1011 $upgrade1011
     */
    public function __construct(
        UpgradeSchema\UpgradeSchema105 $upgrade105,
        UpgradeSchema\UpgradeSchema106 $upgrade106,
        UpgradeSchema\UpgradeSchema107 $upgrade107,
        UpgradeSchema\UpgradeSchema108 $upgrade108,
        UpgradeSchema\UpgradeSchema109 $upgrade109,
        UpgradeSchema\UpgradeSchema1010 $upgrade1010,
        UpgradeSchema\UpgradeSchema1011 $upgrade1011
    ) {
        $this->pool = [
            '1.0.5'  => $upgrade105,
            '1.0.6'  => $upgrade106,
            '1.0.8'  => $upgrade108,
            '1.0.7'  => $upgrade107,
            '1.0.9'  => $upgrade109,
            '1.0.10' => $upgrade1010,
            '1.0.11' => $upgrade1011,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('cms_page'),
                    'alternate_group',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length'   => 255,
                        'nullable' => true,
                        'comment'  => 'Alternate group',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
                    'description_position',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length'   => 5,
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => '1',
                        'comment'  => 'SEO description position',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
                    'description_template',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length'   => '64K',
                        'unsigned' => false,
                        'nullable' => true,
                        'default'  => null,
                        'comment'  => 'Template for adding SEO description',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
                    'apply_for_child_categories',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length'   => '5',
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => '0',
                        'comment'  => 'Apply for child categories',
                    ]
                );
        }

        foreach ($this->pool as $version => $update) {
            if (version_compare($context->getVersion(), $version) < 0) {
                $update->upgrade($setup, $context);
            }
        }

        $installer->endSetup();
    }
}
