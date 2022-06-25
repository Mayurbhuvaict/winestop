<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\GiftCard\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * InstallSchema constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($this->productMetadata->getVersion(), '2.3.0', '>=')) {
            $setup->endSetup();

            return;
        }

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $connection->addColumn($setup->getTable('mageplaza_giftcard_pool'), 'conditions_serialized', [
                'type'    => Table::TYPE_TEXT,
                'length'  => '2M',
                'comment' => 'Condition Serialized',
            ]);

            $connection->addColumn($setup->getTable('mageplaza_giftcard'), 'conditions_serialized', [
                'type'    => Table::TYPE_TEXT,
                'length'  => '2M',
                'comment' => 'Condition Serialized',
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $connection->addIndex(
                $setup->getTable('mageplaza_giftcard'),
                $setup->getIdxName('mageplaza_giftcard', ['pool_id']),
                ['pool_id']
            );

            $connection->addForeignKey(
                $setup->getFkName('mageplaza_giftcard', 'pool_id', 'mageplaza_giftcard_pool', 'pool_id'),
                $setup->getTable('mageplaza_giftcard'),
                'pool_id',
                $setup->getTable('mageplaza_giftcard_pool'),
                'pool_id',
                Table::ACTION_NO_ACTION
            );
        }

        $setup->endSetup();
    }
}
