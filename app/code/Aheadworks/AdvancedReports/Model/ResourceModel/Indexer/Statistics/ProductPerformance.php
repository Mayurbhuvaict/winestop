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
 * Class ProductPerformance
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformance extends AbstractResource
{
    /**
     * @var string
     */
    protected $period;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['payment_method'] = 'order_payment.method';

        $select =
            $this->joinParentItems()
            ->join(
                ['order_payment' => $this->getTable('sales_order_payment')],
                'order_payment.parent_id = order.entity_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields(['order_payment.method']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    /**
     * Retrieve base columns for table
     *
     * @param string $type
     * @return []
     */
    protected function getColumns($type = 'parent')
    {
        $this->period = $this->getPeriod('order.created_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'customer_group_id' => 'order.customer_group_id',
            'product_id' => 'main_table.product_id',
            'product_name' => 'main_table.name',
            'sku' => 'IFNULL(catalog.sku, CONCAT(main_table.sku, " (product was deleted)"))',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        if ($type == 'children') {
            $columns = array_merge($columns, $this->getColumnsForChildrenItems());
        } else {
            $columns = array_merge($columns, $this->getColumnsForParentItems());
        }

        return $columns;
    }

    /**
     * Retrieve base group by for table
     *
     * @param [] $additionalFields
     * @return []
     */
    protected function getGroupByFields($additionalFields)
    {
        return array_merge(
            [
                'order.status',
                $this->period,
                'order.store_id',
                'main_table.product_id',
                'order.base_to_global_rate',
                'order.customer_group_id'
            ],
            $additionalFields
        );
    }

    /**
     * Retrieve columns for parent items query
     *
     * @return []
     */
    private function getColumnsForParentItems()
    {
        $columns = [
            'subtotal' => 'SUM(COALESCE(main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(children.discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(main_table.base_row_total, 0.0)) '
                . '+ SUM(COALESCE(main_table.base_tax_amount, 0.0)) '
                . '- SUM(COALESCE(children.discount_amount, main_table.base_discount_amount, 0.0)) '
                . '+ SUM(COALESCE(main_table.base_discount_tax_compensation_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(children.row_invoiced, main_table.base_row_invoiced, 0.0)) '
                . '+ SUM(COALESCE(children.tax_invoiced, main_table.base_tax_invoiced, 0.0)) '
                . '- SUM(COALESCE(children.discount_invoiced, main_table.base_discount_invoiced, 0.0)) '
                . '+ SUM(COALESCE(main_table.base_discount_tax_compensation_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(children.amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . '+ SUM(COALESCE(children.tax_refunded, main_table.base_tax_refunded, 0.0)) '
                . '+ SUM(COALESCE(main_table.base_discount_tax_compensation_refunded, 0.0)) '
                . '- SUM(COALESCE(children.discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'total_cost' => 'SUM(children.total_cost)',
            'total_revenue_excl_tax' => '(SUM(COALESCE(main_table.base_row_total, 0.0)) '
                . '- (SUM(COALESCE(children.discount_amount, main_table.base_discount_amount, 0.0)) '
                . '- SUM(COALESCE(children.discount_refunded, main_table.base_discount_refunded, 0.0))) '
                . '- SUM(COALESCE(children.amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . '- SUM(COALESCE(main_table.base_discount_tax_compensation_refunded, 0.0)) '
                . '+ SUM(COALESCE(main_table.base_discount_tax_compensation_amount, 0.0)))',
        ];

        return $columns;
    }

    /**
     * Retrieve columns for child items query
     *
     * @return []
     */
    private function getColumnsForChildrenItems()
    {
        $columns = [
            'parent_id' => 'parent.product_id',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0)) '
                . '+ SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0)) '
                . '- SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)) '
                . '+ SUM(COALESCE(
                        configurable.base_discount_tax_compensation_amount,
                        main_table.base_discount_tax_compensation_amount, 
                        0.0
                     )
                ))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0)) '
                . ' + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0)) '
                . ' - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0)) '
                . ' + SUM(COALESCE(
                        configurable.base_discount_tax_compensation_invoiced, 
                        main_table.base_discount_tax_compensation_invoiced, 
                        0.0
                      )
                ))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . '+ SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0)) '
                . '+ SUM(COALESCE(
                        configurable.base_discount_tax_compensation_refunded, 
                        main_table.base_discount_tax_compensation_refunded, 
                        0.0
                  ))'
                . '- SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'total_cost' => $this->getTotalCostFieldChildren(),
            'total_revenue_excl_tax' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0)) '
                . '- (SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)) '
                . '- SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0))) '
                . '- SUM(COALESCE(
                        configurable.base_discount_tax_compensation_refunded, 
                        main_table.base_discount_tax_compensation_refunded, 
                        0.0
                  )) '
                . '- SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . '+ SUM(COALESCE(
                        configurable.base_discount_tax_compensation_amount, 
                        main_table.base_discount_tax_compensation_amount, 
                        0.0
                     )
                ))'
        ];

        return $columns;
    }

    /**
     * Retrieve base query
     *
     * @return \Magento\Framework\DB\Select
     */
    private function baseQuery()
    {
        $this->disableStagingPreview();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order_item')], [])
            ->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )->joinLeft(
                ['catalog' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = catalog.entity_id',
                []
            );
        return $select;
    }

    /**
     * Retrieve query by join parent items
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function joinParentItems()
    {
        $select = $this->baseQuery()
            ->joinLeft(
                ['children' => $this->getSubQueryForParentItems()],
                'main_table.item_id = children.parent_id',
                []
            )->where('main_table.parent_item_id IS NULL');
        return $select;
    }

    /**
     * Retrieve sub query for joinParentItems method
     *
     * @return \Zend_Db_Expr
     */
    protected function getSubQueryForParentItems()
    {
        $columns = [
            'discount_amount' => 'IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), 
                item.base_discount_amount)',
            'discount_invoiced' => 'IF(item.base_discount_invoiced = 0, SUM(item2.base_discount_invoiced), 
                item.base_discount_invoiced)',
            'discount_refunded' => 'IF(item.base_discount_refunded = 0, SUM(item2.base_discount_refunded), 
                item.base_discount_refunded)',
            'row_invoiced' => 'IF(item.base_row_invoiced = 0, SUM(item2.base_row_invoiced), 
                item.base_row_invoiced)',
            'amount_refunded' => 'IF(item.base_amount_refunded = 0, SUM(item2.base_amount_refunded), 
                item.base_amount_refunded)',
            'tax_invoiced' => 'IF(item.base_tax_invoiced = 0, SUM(item2.base_tax_invoiced), 
                item.base_tax_invoiced)',
            'tax_refunded' => 'IF(item.base_tax_refunded = 0, SUM(item2.base_tax_refunded), 
                item.base_tax_refunded)',
            'parent_id' => 'item.item_id',
            'total_cost' => $this->getTotalCostFieldParent()
        ];
        $orderItemTable = $this->getTable('sales_order_item');

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order')], [])
            ->columns($columns)
            ->join(
                ['item' => $orderItemTable],
                '(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)',
                []
            )->joinLeft(
                ['item2' => $orderItemTable],
                '(item2.order_id = main_table.entity_id AND item2.parent_item_id IS NOT NULL AND '
                . 'item2.parent_item_id = item.item_id AND item.product_type IN ("configurable", "bundle"))',
                []
            )->joinLeft(
                ['bundle_item_price' => $this->getBundleItemsPrice()],
                '(main_table.entity_id = bundle_item_price.order_id AND '
                . 'item.item_id = bundle_item_price.parent_item_id)',
                []
            )->group(['item.item_id']);
        return new \Zend_Db_Expr('(' . $select . ')');
    }

    /**
     * Retrieve query by join children items
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function joinChildrenItems()
    {
        $this->disableStagingPreview();
        $orderItemTable = $this->getTable('sales_order_item');

        $select =
            $this->baseQuery()
                ->joinLeft(
                    ['configurable' => $orderItemTable],
                    '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id 
                    AND configurable.product_type IN ("configurable"))',
                    []
                )->joinLeft(
                    ['parent' => $orderItemTable],
                    '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                    []
                )->joinLeft(
                    ['bundle_item_price' => $this->getBundleItemsPrice()],
                    '(order.entity_id = bundle_item_price.order_id AND '
                    . 'main_table.item_id = bundle_item_price.parent_item_id)',
                    []
                )->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
                ->where('(main_table.product_type <> "configurable")');
        return $select;
    }

    /**
     * Retrieve query for counting Total Cost field
     *
     * @return \Zend_Db_Expr
     */
    private function getTotalCostFieldParent()
    {
        // Note: for Configurable products and Bundle products with fixed price, qty refunded saved in parent item
        $itemQty = '(item.qty_ordered - item.qty_refunded)';
        $item2Qty = '(item2.qty_ordered - item2.qty_refunded)';

        $totalCostField = new \Zend_Db_Expr(
            'CASE item.product_type '
            . 'WHEN "configurable" '
            . 'THEN COALESCE(SUM(item2.base_cost * ' . $itemQty . '), item.base_cost * ' . $itemQty . ', 0) '
            // If bundle with fixed price (bundle_item_price.price = 0) or bundle with dynamic price
            . 'WHEN "bundle" THEN IF(bundle_item_price.price = 0, '
            . 'COALESCE(item.base_cost * ' . $itemQty . ', SUM(item2.base_cost * ' . $itemQty . '), 0), '
            . 'COALESCE(SUM(item2.base_cost * ' . $item2Qty . '), 0)) '
            . 'ELSE COALESCE(item.base_cost, 0) * ' . $itemQty . ' END'
        );
        return $totalCostField;
    }

    /**
     * Retrieve query for counting Total Cost field
     *
     * @return \Zend_Db_Expr
     */
    private function getTotalCostFieldChildren()
    {
        // Note: for Configurable products and Bundle products with fixed price, qty refunded saved in parent item
        $parentQty = '(parent.qty_ordered - parent.qty_refunded)';
        $mainQty = '(main_table.qty_ordered - main_table.qty_refunded)';

        $totalCostField = new \Zend_Db_Expr(
            'SUM(CASE COALESCE(parent.product_type, main_table.product_type) '
            . 'WHEN "configurable" THEN '
            . 'COALESCE(main_table.base_cost * ' . $parentQty . ', parent.base_cost * ' . $parentQty . ', 0) '
            // If bundle with fixed price (bundle_item_price.price = 0) or bundle with dynamic price
            . 'WHEN "bundle" THEN IF(bundle_item_price.price = 0, '
            . 'COALESCE(main_table.base_cost * ' . $mainQty . ', bundle_item_price.items_cost * ' . $mainQty . ', 0), '
            // Fills by zero bundle child cost for bundle products with fixed price
            . 'IF(main_table.base_row_total = 0, 0, COALESCE(main_table.base_cost * ' . $mainQty . ', 0))) '
            . 'ELSE COALESCE(main_table.base_cost, 0) * ' . $mainQty . ' END)'
        );
        return $totalCostField;
    }
}
