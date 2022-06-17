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

































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\PaymentType;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\PaymentType as ResourcePaymentType;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\PaymentType
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourcePaymentType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->group('method');
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
            'method' => 'method',
            'additional_info' => 'additional_info',
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
}
