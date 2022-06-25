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






































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductPerformance;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductPerformance as ResourceProductPerformance;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductPerformance
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * @var bool
     */
    private $manufacturerFilter;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductPerformance::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->group('product_id');

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
     * Retrieve report total columns
     *
     * @param boolean $addRate
     * @return array
     */
    protected function getTotalColumns($addRate = false)
    {
        $columns = $this->getColumns($addRate);
        unset($columns['sku']);

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $rateField = $this->getRateField($addRate);
        $totalCost = 'SUM(COALESCE(main_table.total_cost' . $rateField . ', 0))';
        $totalRevenueExclTax = 'SUM(COALESCE(main_table.total_revenue_excl_tax' . $rateField . ', 0))';
        $totalProfit = new \Zend_Db_Expr($totalRevenueExclTax . ' - ' . $totalCost);

        $columns = [
            'product_id' => 'product_id',
            'product_name' => 'product_name',
            'sku' => 'sku',
            'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
            'total_cost' => $totalCost,
            'total_revenue_excl_tax' => $totalRevenueExclTax,
            'total_profit' => $totalProfit,
            'total_margin' => new \Zend_Db_Expr('Round(((' . $totalProfit . ') / ' . $totalRevenueExclTax .  ') * 100)')
        ];

        if ($this->manufacturerFilter) {
            $columns['parent_id'] = 'parent_id';
        }
        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'category_id') {
            return $this->addCategoryIdFilter($condition);
        }
        if ($field == 'coupon_code') {
            return $this->addCouponCodeFilter($condition);
        }
        if ($field == 'payment_type') {
            return $this->addPaymentTypeFilter($condition);
        }
        if ($field == 'manufacturer') {
            return $this->addManufacturerFilter($condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add category id filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addCategoryIdFilter($condition)
    {
        $this->changeMainTable('_category');
        $resultCondition = $this->_getConditionSql('category_id', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add coupon code filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addCouponCodeFilter($condition)
    {
        $this->changeMainTable('_coupon_code');
        $resultCondition = $this->_getConditionSql('coupon_code', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add payment type filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addPaymentTypeFilter($condition)
    {
        $resultCondition = $this->_getConditionSql('payment_method', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add manufacturer filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addManufacturerFilter($condition)
    {
        $this->manufacturerFilter = true;
        $this->changeMainTable('_manufacturer');
        $resultCondition = $this->_getConditionSql('manufacturer', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }
}
