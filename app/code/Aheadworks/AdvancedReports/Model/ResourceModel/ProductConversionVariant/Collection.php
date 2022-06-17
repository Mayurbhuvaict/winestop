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























































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversionVariant;

use Aheadworks\AdvancedReports\Model\ResourceModel\AbstractPeriodBasedCollection;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\Conversion as ResourceConversion;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversion as ResourceProductConversion;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductConversion
 */
class Collection extends AbstractPeriodBasedCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductConversion::class);
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
        $this->getSelect()->columns($this->getColumns());
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $ordersCount = 'SUM(main_table.orders_count)';
        $viewsCount = 'SUM(main_table.views_count)';
        return [
            'period' => 'period',
            'product_id' => 'main_table.product_id',
            'views_count' => 'COALESCE(SUM(main_table.views_count), 0)',
            'orders_count' => 'COALESCE(SUM(main_table.orders_count), 0)',
            'conversion_rate' => 'IF(' . $viewsCount . ' > 0, ' .
                'IF(' . $viewsCount . ' < ' . $ordersCount . ', 100, ' .
                $ordersCount . ' / ' . $viewsCount . ' * 100), 0)',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'product_id') {
            $condition = $condition['eq'];
            return $this->addProductFilter($condition['product_id']);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function addExcludeRefundedItemsFilter()
    {
        $query = '(is_refunded = 0 OR is_refunded IS NULL)';
        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];

        return $this;
    }

    /**
     * Add product id filter to collection
     *
     * @param int $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $query = '(main_table.product_id = ' . $productId . ')';
        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addGroupByDay()
    {
        parent::addGroupByDay();
        $this->getSelect()->order('date', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function groupByTable($table)
    {
        parent::groupByTable($table);
        $this->getSelect()->order('start_date', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOrderStatuses()
    {
        $orderStatuses = parent::getOrderStatuses();
        $orderStatuses[] = ResourceConversion::VIEWED_STATUS;
        return $orderStatuses;
    }
}
