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
 * Class Category
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Category extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_category', 'id');
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
            'category' => 'IFNULL(category.value, "Not Set")',
            'category_id' => 'main_table.category_id',
            'order_items_count' => '(SUM(COALESCE(item.qty_ordered, 0)))',
            'subtotal' => 'SUM(COALESCE(item.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(item.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(children.discount_amount, item.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(item.base_row_total, 0.0))
                + SUM(COALESCE(item.base_tax_amount, 0.0))
                + SUM(COALESCE(item.base_discount_tax_compensation_amount, 0.0))
                - SUM(COALESCE(children.discount_amount, item.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(children.row_invoiced, item.base_row_invoiced, 0.0))
                + SUM(COALESCE(children.tax_invoiced, item.base_tax_invoiced, 0.0))
                + SUM(COALESCE(item.base_discount_tax_compensation_invoiced, 0.0))
                - SUM(COALESCE(children.discount_invoiced, item.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(children.amount_refunded, item.base_amount_refunded, 0.0))
                + SUM(COALESCE(children.tax_refunded, item.base_tax_refunded, 0.0))
                + SUM(COALESCE(item.base_discount_tax_compensation_refunded, 0.0))
                - SUM(COALESCE(children.discount_refunded, item.base_discount_refunded, 0.0)))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        $orderTable  = $this->getTable('sales_order');
        $orderItemTable = $this->getTable('sales_order_item');

        /* @var $categoryNameAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $categoryNameAttr = $this->attributeRepository->get('catalog_category', 'name');
        $categoryNameTable = $categoryNameAttr->getBackendTable();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('catalog_category_product')], [])
            ->columns($columns)
            ->joinLeft(
                ['category' => $categoryNameTable],
                'main_table.category_id = category.' . $this->getCatalogLinkField()
                . ' AND category.attribute_id = ' . $categoryNameAttr->getId() . ' AND category.store_id = 0',
                []
            )
            ->joinRight(
                ['item' => $orderItemTable],
                'main_table.product_id = item.product_id',
                []
            )->join(
                ['order' => $orderTable],
                'item.order_id = order.entity_id',
                []
            )
            // For calculated configurable and bundle order items
            ->joinLeft(
                ['children' => new \Zend_Db_Expr(
                    '(SELECT 
                    IF(t_item.base_discount_amount = 0, SUM(t_item2.base_discount_amount), 
                    t_item.base_discount_amount) AS `discount_amount`,
                    IF(t_item.base_discount_invoiced = 0, SUM(t_item2.base_discount_invoiced), 
                    t_item.base_discount_invoiced) AS `discount_invoiced`,
                    IF(t_item.base_discount_refunded = 0, SUM(t_item2.base_discount_refunded), 
                    t_item.base_discount_refunded) AS `discount_refunded`,
                    IF(t_item.base_row_invoiced = 0, SUM(t_item2.base_row_invoiced), 
                    t_item.base_row_invoiced) AS `row_invoiced`,
                    IF(t_item.base_amount_refunded = 0, SUM(t_item2.base_amount_refunded), 
                    t_item.base_amount_refunded) AS `amount_refunded`,
                    IF(t_item.base_tax_invoiced = 0, SUM(t_item2.base_tax_invoiced), 
                    t_item.base_tax_invoiced) AS `tax_invoiced`,
                    IF(t_item.base_tax_refunded = 0, SUM(t_item2.base_tax_refunded), 
                    t_item.base_tax_refunded) AS `tax_refunded`,
                    t_item.item_id AS `parent_id`
                    FROM ' . $orderTable . ' AS `t_order`
                    INNER JOIN ' . $orderItemTable . ' AS `t_item` 
                    ON (t_item.order_id = t_order.entity_id AND t_item.parent_item_id IS NULL)
                    INNER JOIN ' . $orderItemTable . ' AS `t_item2` ON (t_item2.order_id = t_order.entity_id 
                    AND t_item2.parent_item_id IS NOT NULL AND t_item2.parent_item_id = t_item.item_id)
                    GROUP BY t_item.item_id)'
                )],
                'item.item_id = children.parent_id',
                []
            )
            ->where('item.parent_item_id IS NULL')
            ->group(
                [
                    'order.status',
                    $period,
                    'order.store_id',
                    'main_table.category_id',
                    'order.base_to_global_rate',
                    'order.customer_group_id'
                ]
            );
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
