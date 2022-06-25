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























































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Customers;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales as ResourceCustomerSales;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Customers
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * @var array
     */
    private $conditionsForSales = [];

    /**
     * @var boolean
     */
    private $excludeRefunded;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceCustomerSales::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->group([
                'main_table.customer_id',
                'main_table.customer_email'
            ])
        ;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $rateField = $this->getRateField($addRate);

        if ($this->excludeRefunded) {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered - main_table.qty_refunded, 0))';
            $total = 'SUM(COALESCE((main_table.total - main_table.total_refunded)' . $rateField . ', 0))';
            $orderItemsCountLifetime = 'COALESCE(lifetime_sales.qty_ordered - lifetime_sales.qty_refunded, 0)';
            $totalLifetime = 'COALESCE((lifetime_sales.total - lifetime_sales.total_refunded)' .
                $rateField . ', 0)';
        } else {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered, 0))';
            $total = 'SUM(COALESCE(main_table.total' . $rateField . ', 0))';
            $orderItemsCountLifetime = 'COALESCE(lifetime_sales.qty_ordered, 0)';
            $totalLifetime = 'COALESCE(lifetime_sales.total' . $rateField . ', 0)';
        }
        return [
            'customer_id' => "main_table.customer_id",
            'customer_name' => "main_table.customer_name",
            'customer_email' => "main_table.customer_email",
            'customer_group' => "main_table.customer_group",
            'country' => "main_table.country",
            'region' => "main_table.region",
            'phone' => "main_table.phone",
            'total_sales_for_period' => $total,
            'orders_count_for_period' => 'SUM(COALESCE(main_table.orders_count, 0))',
            'order_items_count_for_period' => $orderItemsCount,
            'total_sales_lifetime' => $totalLifetime,
            'orders_count_lifetime' => 'COALESCE(lifetime_sales.orders_count, 0)',
            'order_items_lifetime' => $orderItemsCountLifetime,
            'created_in' => "main_table.created_in",
            'last_login_at' => "main_table.last_login_at",
            'last_order_at' => "main_table.last_order_at",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTotalColumns($addRate = false)
    {
        return [
            'customer_name' => "main_table.customer_name",
            'customer_email' => "main_table.customer_email",
            'customer_group' => "main_table.customer_group",
            'country' => "main_table.country",
            'region' => "main_table.region",
            'phone' => "main_table.phone",
            'total_sales_for_period' => 'SUM(COALESCE(main_table.total_sales_for_period, 0))',
            'orders_count_for_period' => 'SUM(COALESCE(main_table.orders_count_for_period, 0))',
            'order_items_count_for_period' => 'SUM(COALESCE(main_table.order_items_count_for_period, 0))',
            'total_sales_lifetime' => 'SUM(COALESCE(total_sales_lifetime, 0))',
            'orders_count_lifetime' => 'SUM(COALESCE(orders_count_lifetime, 0))',
            'order_items_lifetime' => 'SUM(COALESCE(order_items_lifetime, 0))',
            'created_in' => "main_table.created_in",
            'last_login_at' => "main_table.last_login_at",
            'last_order_at' => "main_table.last_order_at",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'exclude_refunded') {
            if ((int)$condition['eq'] == 1) {
                return $this->addExcludeRefundedItemsFilter();
            } else {
                return $this;
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function addExcludeRefundedItemsFilter()
    {
        $this->excludeRefunded = true;
        $this->getSelect()
            ->where('? > 0', new \Zend_Db_Expr('(main_table.qty_ordered - main_table.qty_refunded)'));

        return $this;
    }

    /**
     * Add range filter to collection
     *
     * @param array $range
     * @return $this
     */
    public function addRangeFilter($range)
    {
        if (null != $range) {
            if (isset($range['from']) && $range['from']) {
                $this->addFieldToFilter('total_sales_for_period', ['gteq' => $range['from']]);
            }
            if (isset($range['to']) && $range['to']) {
                $this->addFieldToFilter('total_sales_for_period', ['lteq' => $range['to']]);
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addOrderStatusFilter()
    {
        $this->conditionsForSales[] = [
            'field' => 'order_status',
            'condition' => ['in' => $this->getOrderStatuses()]
        ];
        return parent::addOrderStatusFilter();
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerGroupFilter($customerGroupsId)
    {
        $this->conditionsForSales[] = [
            'field' => 'customer_group_id',
            'condition' => ['in' => $customerGroupsId]
        ];

        return parent::addCustomerGroupFilter($customerGroupsId);
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreFilter($storeIds)
    {
        $this->conditionsForSales[] = [
            'field' => 'store_id',
            'condition' => ['in' => $storeIds]
        ];

        return parent::addStoreFilter($storeIds);
    }

    /**
     * Get condition for lifetime sales
     *
     * @return string
     */
    protected function getConditionForSales()
    {
        $joinCondition = '1=1';
        foreach ($this->conditionsForSales as $condition) {
            $joinCondition .= ' AND ' . ($condition['condition']
                    ? $this->_getConditionSql($condition['field'], $condition['condition'])
                    : $condition['field']);
        }
        return $joinCondition;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->columns($this->getColumns(true));
        $excludeRefunded = '1=1';
        if ($this->excludeRefunded) {
            $excludeRefunded = '((qty_ordered - qty_refunded) > 0)';
        }
        $this->getSelect()
            ->joinRight(
                ['lifetime_sales' => new \Zend_Db_Expr(
                    '(SELECT customer_id, customer_email, SUM(orders_count) as orders_count, 
                    SUM(qty_ordered) as qty_ordered, SUM(qty_refunded) as qty_refunded, SUM(total) as total, 
                    SUM(total_refunded) as total_refunded
                    FROM ' . $this->getMainTable() . ' WHERE ' . $this->getConditionForSales() .
                    ' AND ' . $excludeRefunded . ' GROUP BY customer_id, customer_email)'
                )],
                'IF(main_table.customer_id IS NULL, 
                lifetime_sales.customer_email = main_table.customer_email AND lifetime_sales.customer_id IS NULL,
                lifetime_sales.customer_id = main_table.customer_id)',
                []
            );
        parent::_renderFiltersBefore();
    }
}
