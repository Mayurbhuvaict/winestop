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

/**
 * Class CustomerSales
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class CustomerSales extends AbstractResource
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
        $this->_init('aw_arep_customer_sales', 'id');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function process()
    {
        $this->period = $this->getPeriod('main_table.created_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'main_table.store_id',
            'order_status' => 'main_table.status',
            'customer_id' => 'main_table.customer_id',
            'customer_email' => 'IF(main_table.customer_id IS NULL, main_table.customer_email, customer.email)',
            'customer_name' => 'IFNULL(CONCAT(customer.firstname, " ", customer.lastname), '
                . 'CONCAT(billing_address.firstname, " ", billing_address.lastname))',
            'customer_group_id' =>
                'IF(main_table.customer_id IS NULL, main_table.customer_group_id, customer.group_id)',
            'customer_group' => 'c_group.customer_group_code',
            'country' => 'COALESCE(customer_address.country_id, billing_address.country_id, "")',
            'region' => 'COALESCE(customer_address.region, billing_address.region, "")',
            'phone' => 'COALESCE(customer_address.telephone, billing_address.telephone, "")',
            'created_in' => 'customer.created_in',
            'last_login_at' => 'customer_log.last_login_at',
            'last_order_at' => 'COALESCE(order_created_registered.last_order_date, ' .
                'order_created_guest.last_order_date)',
            'orders_count' => 'COUNT(main_table.entity_id)',
            'qty_ordered' => 'SUM(order_items.qty_ordered)',
            'qty_refunded' => 'SUM(order_items.qty_refunded)',
            'total' => 'SUM(COALESCE(main_table.base_grand_total, 0.0))',
            'total_refunded' => 'SUM(COALESCE(main_table.base_total_refunded, 0.0))',
            'to_global_rate' => 'main_table.base_to_global_rate'
        ];

        $orderItemTable = $this->getTable('sales_order_item');
        $salesOrderAddress = $this->getTable('sales_order_address');
        $customer = $this->getTable('customer_entity');
        $customerAddress = $this->getTable('customer_address_entity');
        $customerLog = $this->getTable('customer_log');
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order')], [])
            ->join(
                ['order_items' => new \Zend_Db_Expr(
                    '(SELECT order_id, SUM(qty_ordered) as qty_ordered, SUM(qty_invoiced) as qty_invoiced, 
                    SUM(qty_shipped) as qty_shipped, SUM(qty_refunded) as qty_refunded 
                    FROM ' . $orderItemTable . ' WHERE parent_item_id IS NULL GROUP BY order_id)'
                )],
                'order_items.order_id = main_table.entity_id',
                []
            )->joinLeft(
                ['customer' => $customer],
                'customer.entity_id = main_table.customer_id',
                []
            )->joinLeft(
                ['c_group' => $this->getTable('customer_group')],
                'IF(main_table.customer_id IS NULL, main_table.customer_group_id = c_group.customer_group_id,
                customer.group_id = c_group.customer_group_id)',
                []
            )->joinLeft(
                ['billing_address' => $salesOrderAddress],
                'billing_address.parent_id = main_table.entity_id AND billing_address.address_type = "billing"',
                []
            )->joinLeft(
                ['customer_log' => $customerLog],
                'customer_log.customer_id = main_table.customer_id',
                []
            )->joinLeft(
                ['customer_address' => $customerAddress],
                'customer_address.entity_id = customer.default_billing',
                []
            )->joinLeft(
                ['order_created_guest' => new \Zend_Db_Expr(
                    '(SELECT customer_id, customer_email, store_id, MAX(created_at) AS last_order_date ' .
                    'FROM ' . $this->getTable('sales_order') . ' GROUP BY customer_id, customer_email, store_id)'
                )],
                'order_created_guest.customer_email = main_table.customer_email AND 
                order_created_guest.store_id = main_table.store_id AND order_created_guest.customer_id IS NULL',
                []
            )->joinLeft(
                ['order_created_registered' => new \Zend_Db_Expr(
                    '(SELECT customer_id, customer_email, store_id, MAX(created_at) AS last_order_date ' .
                    'FROM ' . $this->getTable('sales_order') . ' GROUP BY customer_id, customer_email, store_id)'
                )],
                'order_created_registered.customer_id = main_table.customer_id AND
                order_created_registered.customer_email = main_table.customer_email AND 
                order_created_registered.store_id = main_table.store_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields([]));

        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
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
                'main_table.status',
                $this->period,
                'main_table.store_id',
                'main_table.base_to_global_rate',
                'customer_group_id',
                'customer_id',
                'customer_email'
            ],
            $additionalFields
        );
    }
}
