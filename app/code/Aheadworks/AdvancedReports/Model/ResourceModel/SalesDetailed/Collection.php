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

















































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed as ResourceSalesDetailed;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceSalesDetailed::class);
    }

    /**
     * {@inheritdoc}
     */
    public function addExcludeRefundedItemsFilter()
    {
        $this->getSelect()
            ->where('? > 0', new \Zend_Db_Expr('(qty_ordered - qty_refunded)'));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], []);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->columns($this->getColumns(true));
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        return array_merge($this->getCounterColumns($addRate), $this->getOtherTypeColumns());
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getTotalColumns($addRate = false)
    {
        $counterColumns = $this->getCounterColumns($addRate);
        foreach ($counterColumns as $alias => &$item) {
            $item = 'SUM(' . $item . ')';
        }
        return $counterColumns;
    }

    /**
     * Retrieve counter columns
     *
     * @param boolean $addRate
     * @return array
     */
    private function getCounterColumns($addRate)
    {
        $rateField = $this->getRateField($addRate);
        return [
            'qty_ordered' => 'qty_ordered',
            'qty_invoiced' => 'qty_invoiced',
            'qty_shipped' => 'qty_shipped',
            'qty_refunded' => 'qty_refunded',
            'item_price' => 'COALESCE(main_table.item_price' . $rateField . ', 0)',
            'item_cost' => 'COALESCE(main_table.item_cost' . $rateField . ', 0)',
            'subtotal' => 'COALESCE(main_table.subtotal' . $rateField . ', 0)',
            'discount' => 'COALESCE(main_table.discount' . $rateField . ', 0)',
            'tax' => 'COALESCE(main_table.tax' . $rateField . ', 0)',
            'total' => 'COALESCE(main_table.total' . $rateField . ', 0)',
            'total_incl_tax' => 'COALESCE(main_table.total_incl_tax' . $rateField . ', 0)',
            'invoiced' => 'COALESCE(main_table.invoiced' . $rateField . ', 0)',
            'tax_invoiced' => 'COALESCE(main_table.tax_invoiced' . $rateField . ', 0)',
            'invoiced_incl_tax' => 'COALESCE(main_table.invoiced_incl_tax' . $rateField . ', 0)',
            'refunded' => 'COALESCE(main_table.refunded' . $rateField . ', 0)',
            'tax_refunded' => 'COALESCE(main_table.tax_refunded' . $rateField . ', 0)',
            'refunded_incl_tax' => 'COALESCE(main_table.refunded_incl_tax' . $rateField . ', 0)',
            'base_grand_total' => 'COALESCE(main_table.base_grand_total, 0)',
            'shipping_amount' => 'COALESCE(main_table.shipping_amount' . $rateField . ', 0)',
            'base_tax' => 'COALESCE(main_table.tax, 0)',
            'base_tax_real_amount' => 'COALESCE(main_table.base_tax_real_amount, 0)',
            'customer_balance_refunded' => 'COALESCE(main_table.customer_balance_refunded' . $rateField . ', 0)',
        ];
    }

    /**
     * Retrieve other type columns
     *
     * @return array
     */
    private function getOtherTypeColumns()
    {
        return [
            'order_status',
            'order_date',
            'order_id',
            'order_increment_id',
            'product_id',
            'product_name',
            'sku',
            'manufacturer',
            'customer_id',
            'customer_email',
            'customer_name',
            'customer_group',
            'country',
            'shipping_region',
            'shipping_city',
            'shipping_zip_code',
            'address',
            'phone',
            'coupon_code',
            'shipping_information',
            'order_modified_date',
            'tax_percent',
            'tax_code',
            'billing_region',
            'billing_city',
            'billing_zip_code'
        ];
    }
}
