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
 * Class Manufacturer
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Manufacturer extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_manufacturer', 'id');
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
            'manufacturer' => $this->getManufacturerAttribute() ?
                'IFNULL(manufacturer_value.value, "Not Set")' :
                'COALESCE("Not Set")',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))
                + SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))
                + SUM(COALESCE(
                        configurable.base_discount_tax_compensation_amount,
                        main_table.base_discount_tax_compensation_amount,
                        0.0
                  ))
                - SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0))
                + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0))
                + SUM(COALESCE(
                        configurable.base_discount_tax_compensation_invoiced,
                        main_table.base_discount_tax_compensation_invoiced, 
                        0.0
                  ))
                - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . ' + SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0)) '
                . ' + SUM(COALESCE(
                        configurable.base_discount_tax_compensation_refunded, 
                        main_table.base_discount_tax_compensation_refunded, 
                        0.0
                  )) '
                . ' - SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        $orderItemTable = $this->getTable('sales_order_item');

        $select = $this->getConnection()->select()
            ->from(['main_table' => $orderItemTable], [])
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
                ['configurable' => $orderItemTable],
                '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id '
                . 'AND configurable.product_type IN ("configurable"))',
                []
            )
            ->joinLeft(
                ['parent' => $orderItemTable],
                '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                []
            )
            ->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
            ->where('(main_table.product_type <> "configurable")')
            ->group($this->getGroupBy($period));

        $select = $this->addManufacturer($select);

        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    private function getGroupBy($period)
    {
        if ($this->getManufacturerAttribute()) {
            $groupBy = [
                'order.status',
                $period,
                'order.store_id',
                'item_manufacturer.value',
                'order.base_to_global_rate',
                'order.customer_group_id'
            ];
        } else {
            $groupBy = [
                'order.status',
                $period,
                'order.store_id',
                'order.base_to_global_rate',
                'order.customer_group_id'
            ];
        }

        return $groupBy;
    }

    /**
     * Add manufacturer
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    private function addManufacturer($select)
    {
        /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $manufacturerAttr = $this->getManufacturerAttribute();
        if ($manufacturerAttr) {
            $manufacturerTable = $manufacturerAttr->getBackendTable();
            $select
                ->joinLeft(
                    ['product_entity' => $this->getTable('catalog_product_entity')],
                    'product_entity.entity_id = main_table.product_id',
                    []
                )
                ->joinLeft(
                    ['item_manufacturer' => $manufacturerTable],
                    'item_manufacturer.' . $this->getCatalogLinkField() . ' = product_entity.'
                    . $this->getCatalogLinkField() . ' AND item_manufacturer.attribute_id = '
                    . $manufacturerAttr->getId(),
                    []
                )->joinLeft(
                    ['manufacturer_value' => $this->getTable('eav_attribute_option_value')],
                    'item_manufacturer.value = manufacturer_value.option_id AND manufacturer_value.store_id = 0',
                    []
                );
        }

        return $select;
    }
}
