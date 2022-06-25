<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */











































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductVariantPerformance
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductVariantPerformance extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_variant_performance', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $period = $this->getPeriod('order.created_at');
        $columns = [
            'period' => $period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'customer_group_id' => 'order.customer_group_id',
            'product_id' => 'main_table.product_id',
            'product_name' => 'IFNULL(main_table.name, parent.name)',
            'sku' => 'IFNULL(main_table.sku, parent.sku)',
            'parent_id' => 'main_table.parent_item_id',
            'parent_product_id' => 'parent.product_id',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))
                + SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))
                - SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0))
                + SUM(
                    COALESCE(
                        configurable.base_discount_tax_compensation_amount,
                        main_table.base_discount_tax_compensation_amount,
                        0.0
                    )
                ))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0))
                + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0))
                - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0))
                + SUM(
                    COALESCE(
                        configurable.base_discount_tax_compensation_invoiced,
                        main_table.base_discount_tax_compensation_invoiced, 
                        0.0
                    )
                ))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0))
                + SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0))
                + SUM(COALESCE(
                        configurable.base_discount_tax_compensation_refunded, 
                        main_table.base_discount_tax_compensation_refunded, 
                        0.0
                  ))
                - SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order_item')], [])
            ->columns($columns)
            ->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )->joinLeft(
                ['bundle_item_price' => $this->getBundleItemsPrice()],
                '(order.entity_id = bundle_item_price.order_id AND '
                . 'main_table.item_id = bundle_item_price.parent_item_id)',
                []
            )->joinLeft(
                ['configurable' => $this->getTable('sales_order_item')],
                '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id 
                    AND configurable.product_type IN ("configurable"))',
                []
            )->joinLeft(
                ['parent' => $this->getTable('sales_order_item')],
                '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                []
            )
            ->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
            ->where('(main_table.product_type <> "configurable")')
            ->group([
                'order.status',
                $period,
                'order.store_id',
                'main_table.sku',
                'order.base_to_global_rate',
                'order.customer_group_id'
            ]);
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
