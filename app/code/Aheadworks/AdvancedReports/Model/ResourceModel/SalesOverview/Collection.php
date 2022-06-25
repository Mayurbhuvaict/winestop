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



























































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview as ResourceSalesOverview;
use Aheadworks\AdvancedReports\Model\ResourceModel\AbstractPeriodBasedCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview
 */
class Collection extends AbstractPeriodBasedCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceSalesOverview::class);
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
        $orderItemsCount = 'SUM(COALESCE(main_table.order_items_count, 0))';
        $orderCount = 'SUM(COALESCE(main_table.orders_count, 0))';
        $rateField = $this->getRateField($addRate);
        return [
            'orders_count' => $orderCount,
            'order_items_count' => $orderItemsCount,
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'shipping' => 'SUM(COALESCE(main_table.shipping' . $rateField . ', 0))',
            'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
            'other_discount' => 'SUM(COALESCE(main_table.other_discount' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
            'avg_order_amount' => '(IF(' . $orderCount . ' > 0, SUM(COALESCE(main_table.total' . $rateField . ', 0)) / '
                . $orderCount . ', 0.0))',
            'avg_item_final_price' => '(IF(' . $orderItemsCount . ' > 0, 
                        SUM(COALESCE(main_table.subtotal' . $rateField . ', 0)) / ' . $orderItemsCount . ', 0.0))'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'coupon_code') {
            return $this->addCouponCodeFilter($condition);
        }
        if ($field == 'payment_type') {
            return $this->addPaymentCodeFilter($condition);
        }
        return parent::addFieldToFilter($field, $condition);
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
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.coupon_code',
            'condition' => $condition
        ];
        return $this;
    }

    /**
     * Add payment code filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addPaymentCodeFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.payment_method',
            'condition' => $condition
        ];
        return $this;
    }
}
