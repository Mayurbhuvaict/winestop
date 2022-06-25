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

use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;

/**
 * Class SalesDetailed
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class SalesDetailed extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_sales_detailed', 'id');
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Exception
     */
    protected function process()
    {
        $columns = $this->getColumns();

        $orderItemTable = $this->getTable('sales_order_item');
        $orderTaxItemTable = $this->getTable('sales_order_tax_item');
        $salesOrderAddress = $this->getTable('sales_order_address');
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order')], [])
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
            )->joinLeft(
                ['catalog_product' => $this->getTable('catalog_product_entity')],
                'item.product_id = catalog_product.entity_id',
                []
            )->joinLeft(
                ['c_group' => $this->getTable('customer_group')],
                'IFNULL(main_table.customer_group_id, ' .
                CustomerGroup::NOT_LOGGED_IN_ID . ') = c_group.customer_group_id',
                []
            )->joinLeft(
                ['shipping_address' => $salesOrderAddress],
                'shipping_address.parent_id = main_table.entity_id AND shipping_address.address_type = "shipping"',
                []
            )->joinLeft(
                ['billing_address' => $salesOrderAddress],
                'billing_address.parent_id = main_table.entity_id AND billing_address.address_type = "billing"',
                []
            )
            ->joinLeft(['tax_item' => $orderTaxItemTable], 'tax_item.item_id = item.item_id', [])
            ->joinLeft(['tax_item2' => $orderTaxItemTable], 'tax_item2.item_id = item2.item_id', [])
            ->columns($columns)
            ->group('item.item_id');

        $select = $this->addManufacturer($select);
        $select = $this->addProductTaxClass($select);

        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    /**
     * Get columns
     *
     * @return array
     */
    private function getColumns()
    {
        $period = $this->getPeriod('main_table.created_at');
        $columns = [
            'period' => $period,
            'store_id' => 'main_table.store_id',
            'order_status' => 'main_table.status',
            'order_id' => 'main_table.entity_id',
            'order_increment_id' => 'main_table.increment_id',
            'order_date' => 'main_table.created_at',
            'product_id' => 'IFNULL(item.product_id, item2.product_id)',
            'product_name' => 'item.name',
            'sku' => 'IFNULL(item.sku, catalog_product.sku)',
            'manufacturer' => $this->getManufacturerAttribute() ?
                'IFNULL(manufacturer_value.value, "Not Set")' :
                'COALESCE("Not Set")',
            'customer_id' => 'main_table.customer_id',
            'customer_email' => 'main_table.customer_email',
            'customer_name' => 'IFNULL(CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname), '
                . 'CONCAT(billing_address.firstname, " ", billing_address.lastname))',
            'customer_group_id' => 'main_table.customer_group_id',
            'customer_group' => 'c_group.customer_group_code',
            'country' => 'COALESCE(shipping_address.country_id, billing_address.country_id, "")',
            'shipping_region' => 'COALESCE(shipping_address.region, "")',
            'shipping_city' => 'COALESCE(shipping_address.city, "")',
            'shipping_zip_code' => 'COALESCE(shipping_address.postcode, "")',
            'address' => 'COALESCE(shipping_address.street, billing_address.street, "")',
            'phone' => 'COALESCE(shipping_address.telephone, billing_address.telephone, "")',
            'coupon_code' => 'main_table.coupon_code',
            'qty_ordered' => 'COALESCE(IFNULL(item.qty_ordered, item2.qty_ordered), 0)',
            'qty_invoiced' => 'COALESCE(IFNULL(item.qty_invoiced, item2.qty_invoiced), 0)',
            'qty_shipped' => 'COALESCE(IFNULL(item.qty_shipped, item2.qty_shipped), 0)',
            'qty_refunded' => 'COALESCE(IFNULL(item.qty_refunded, item2.qty_refunded), 0)',
            'item_price' => 'COALESCE(IFNULL(item.base_price, item2.base_price), 0)',
            'item_cost' => new \Zend_Db_Expr(
                'CASE item.product_type '
                . 'WHEN "configurable" THEN COALESCE(SUM(item2.base_cost), item.base_cost, 0) '
                // If bundle with fixed (bundle_item_price.price = 0) price or bundle with dynamic price
                . 'WHEN "bundle" THEN IF(bundle_item_price.price = 0, '
                . 'COALESCE(item.base_cost, SUM(item2.base_cost), 0), COALESCE(SUM(item2.base_cost), 0)) '
                . 'ELSE COALESCE(item.base_cost, 0) END'
            ),
            'subtotal' => 'COALESCE((IFNULL(item.qty_ordered, item2.qty_ordered) '
                . '* IFNULL(item.base_price, item2.base_price)), 0)',
            'discount' => 'COALESCE((IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount)), 0)',
            'tax' => 'COALESCE((IFNULL(item.base_tax_amount, item2.base_tax_amount)), 0)',
            'total' => 'COALESCE((IFNULL(item.base_row_total, item2.base_row_total) '
                . '+ IFNULL(item.base_discount_tax_compensation_amount, 0.0000) '
                . '+ IFNULL(COALESCE(item.base_weee_tax_applied_amount, 0), '
                . 'COALESCE(item2.base_weee_tax_applied_amount, 0)) '
                . '- COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0)), 0)',
            'total_incl_tax' => '(IFNULL(item.base_row_total, item2.base_row_total) '
                . '+ IFNULL(item.base_tax_amount, item2.base_tax_amount) '
                . '+ IFNULL(item.base_discount_tax_compensation_amount, 0.0000) '
                . '+ IFNULL(COALESCE(item.base_weee_tax_applied_amount, 0), '
                . 'COALESCE(item2.base_weee_tax_applied_amount, 0)) '
                . '- COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0))',
            'invoiced' => 'COALESCE((COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_row_invoiced), NULL), item.base_row_invoiced, item2.base_row_invoiced) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_invoiced, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_invoiced, 0)) '
                . '- COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_discount_invoiced), NULL), item.base_discount_invoiced, '
                . 'item2.base_discount_invoiced)), 0)',
            'tax_invoiced' => 'COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_tax_invoiced), NULL), item.base_tax_invoiced, item2.base_tax_invoiced, 0)',
            'invoiced_incl_tax' => 'COALESCE((COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_row_invoiced), NULL), item.base_row_invoiced, item2.base_row_invoiced) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_invoiced, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_invoiced, 0)) '
                . '+ COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_tax_invoiced), NULL), item.base_tax_invoiced, item2.base_tax_invoiced) '
                . '- COALESCE(IF(item.product_type = "bundle" AND item2.base_price > 0, '
                . 'SUM(item2.base_discount_invoiced), NULL), item.base_discount_invoiced, '
                . 'item2.base_discount_invoiced)), 0)',
            'refunded' => 'COALESCE(((IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), 1, 0) '
                . '* ((IFNULL(item.qty_refunded, item2.qty_refunded) / IFNULL(item.qty_invoiced, item2.qty_invoiced)) '
                . '* (IFNULL(item.qty_invoiced, item2.qty_invoiced) * IFNULL(item.base_price, item2.base_price) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_refunded, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_refunded, 0)) '
                . '- ABS(COALESCE(IF(item.base_discount_amount = 0, SUM(item2.base_discount_amount), '
                . 'item.base_discount_amount), 0)))))), 0)',
            'tax_refunded' => 'COALESCE(IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), '
                . '(IFNULL(item.qty_refunded, item2.qty_refunded) '
                . '/ IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_tax_invoiced, item2.base_tax_invoiced)), 0), 0)',
            'refunded_incl_tax' => 'COALESCE(((IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), 1, 0) '
                . '* ((IFNULL(item.qty_refunded, item2.qty_refunded) * (IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_price, item2.base_price) - ABS(COALESCE(IF(item.base_discount_amount = 0, '
                . 'SUM(item2.base_discount_amount), item.base_discount_amount), 0))) '
                . '/ IFNULL(item.qty_invoiced, item2.qty_invoiced)) '
                . '+ IFNULL(COALESCE(item.base_discount_tax_compensation_refunded, 0), '
                . 'COALESCE(item2.base_discount_tax_compensation_refunded, 0)) '
                . '+ IF((IFNULL(item.qty_refunded, item2.qty_refunded) > 0), '
                . '(IFNULL(item.qty_refunded, item2.qty_refunded) / IFNULL(item.qty_invoiced, item2.qty_invoiced) '
                . '* IFNULL(item.base_tax_invoiced, item2.base_tax_invoiced)), 0)))), 0)',
            'to_global_rate' => 'main_table.base_to_global_rate',
            'base_grand_total' => 'COALESCE(main_table.base_grand_total, 0)',
            'shipping_information' => 'COALESCE(main_table.shipping_description, "")',
            'shipping_amount' => 'COALESCE(main_table.base_shipping_amount, 0)',
            'order_modified_date' => 'main_table.updated_at',
            'tax_code' => $this->getTaxClassAttribute() ?
                'IFNULL(tax_class_value_table.class_name, "Not Set")' :
                'COALESCE("Not Set")',
            'tax_percent' => 'COALESCE(IFNULL(item.tax_percent, item2.tax_percent), 0)',
            'base_tax_real_amount' => 'COALESCE(IFNULL(tax_item.real_base_amount, tax_item2.real_base_amount), 0)',
            'billing_region' => 'COALESCE(billing_address.region, "")',
            'billing_city' => 'COALESCE(billing_address.city, "")',
            'billing_zip_code' => 'COALESCE(billing_address.postcode, "")',
            'customer_balance_refunded' => $this->getConnection()->tableColumnExists(
                $this->getTable('sales_order'),
                'bs_customer_bal_total_refunded'
            ) ? 'COALESCE(main_table.bs_customer_bal_total_refunded, 0)' : 'COALESCE(0)'
        ];

        return $columns;
    }

    /**
     * Add manufacturer
     *
     * @param Select $select
     * @return Select
     * @throws \Exception
     */
    private function addManufacturer($select)
    {
        /* @var $manufacturerAttr EavAttribute */
        $manufacturerAttr = $this->getManufacturerAttribute();
        if ($manufacturerAttr) {
            $manufacturerTable = $manufacturerAttr->getBackendTable();
            $select
                ->joinLeft(
                    ['product_entity' => $this->getTable('catalog_product_entity')],
                    'product_entity.entity_id = item.product_id',
                    []
                )->joinLeft(
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

    /**
     * Add product tax class
     *
     * @param Select $select
     * @return Select
     * @throws \Exception
     */
    private function addProductTaxClass($select)
    {
        /* @var $taxClassAttribute EavAttribute */
        $taxClassAttribute = $this->getTaxClassAttribute();
        if ($taxClassAttribute) {
            $taxClassTable = $taxClassAttribute->getBackendTable();
            $select
                ->joinLeft(
                    ['tax_class_attr_tbl' => $taxClassTable],
                    'tax_class_attr_tbl.' . $this->getCatalogLinkField() . ' = item.product_id '
                    . 'AND tax_class_attr_tbl.attribute_id = ' . $taxClassAttribute->getId(),
                    []
                )
                ->joinLeft(
                    ['tax_class_value_table' => $this->getTable('tax_class')],
                    'tax_class_attr_tbl.value = tax_class_value_table.class_id AND tax_class_attr_tbl.store_id = 0',
                    []
                );
        }

        return $select;
    }
}
