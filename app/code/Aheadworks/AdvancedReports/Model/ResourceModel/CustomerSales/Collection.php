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





















































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales as ResourceCustomerSales;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * @var string
     */
    private $totalSales;

    /**
     * @var int
     */
    private $websiteId;

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
            ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $orderCount = 'SUM(COALESCE(main_table.orders_count, 0))';
        $rateField = $this->getRateField($addRate);

        if ($this->excludeRefunded) {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered - main_table.qty_refunded, 0))';
            $total = 'SUM(COALESCE((main_table.total - main_table.total_refunded)' . $rateField . ', 0))';
        } else {
            $orderItemsCount = 'SUM(COALESCE(main_table.qty_ordered, 0))';
            $total = 'SUM(COALESCE(main_table.total' . $rateField . ', 0))';
        }
        return [
            'customer_id' => 'main_table.customer_id',
            'customer_email' => 'main_table.customer_email',
            'orders_count' => $orderCount,
            'order_items_count' => $orderItemsCount,
            'total_sales' => $total,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTotalColumns($addRate = false)
    {
        $totalSales = 'SUM(COALESCE(main_table.total_sales, 0))';
        return [
            'sales_range' => new \Zend_Db_Expr("'All'"),
            'customers_count' => 'COUNT(COALESCE(main_table.customer_email, 0))',
            'orders_count' => 'SUM(COALESCE(main_table.orders_count, 0))',
            'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
            'total_sales_percent' => new \Zend_Db_Expr("IF(" . $totalSales . " > 0, 100, 0)"),
            'total_sales' => $totalSales,
        ];
    }

    /**
     * Set website id
     *
     * @param int  $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addExcludeRefundedItemsFilter()
    {
        $this->excludeRefunded = true;
        $this->getSelect()->where('? > 0', new \Zend_Db_Expr('(qty_ordered - qty_refunded)'));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        if (!$this->collectionSelect) {
            $this->collectionSelect = clone $this;
            $this->renderSelect($this->collectionSelect->getSelect());
        }

        $collectionSelect = clone $this->collectionSelect->getSelect();

        $columns = $this->getColumns(true);
        $collectionSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->columns($columns);

        $totalSelect = clone $this->getSelect();
        $totalSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::FROM)
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::WHERE)
            ->reset(\Magento\Framework\DB\Select::ORDER)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect))],
                $this->getTotalColumns()
            );
        $result = $this->getConnection()->fetchRow($totalSelect) ?: [];
        if (isset($result['total_sales'])) {
            $this->totalSales = $result['total_sales'];
        } else {
            $this->totalSales = 0;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->columns($this->getColumns(true));
        if (!$this->collectionSelect) {
            $this->collectionSelect = clone $this;
            $this->renderSelect($this->collectionSelect->getSelect());
        }

        $rangeSelect = $this->getSelectByRange($this->collectionSelect->getSelect());

        $rangeFrom = "COALESCE(main_table.range_from, all_ranges.range_from)";
        $rangeTo = "COALESCE(main_table.range_to, all_ranges.range_to)";
        $this->getSelect()
            ->reset()
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $rangeSelect))],
                []
            )
            ->joinRight(
                ['all_ranges' => $this->getTable('aw_arep_customer_sales_range')],
                'all_ranges.range_id = main_table.range_id',
                []
            )
            ->where('all_ranges.website_id = ?', $this->websiteId)
            ->columns(
                [
                    'sales_range' => new \Zend_Db_Expr(
                        "IF(" . $rangeTo . " IS NULL, CONCAT(" . $rangeFrom . ", '+'),
                        CONCAT(" . $rangeFrom . ", ' - ', " . $rangeTo . "))"
                    ),
                    'range_from' => "COALESCE(main_table.range_from, all_ranges.range_from)",
                    'range_to' => "COALESCE(main_table.range_to, all_ranges.range_to)",
                    'customers_count' => 'COALESCE(main_table.customers_count, 0)',
                    'orders_count' => 'COALESCE(main_table.orders_count, 0)',
                    'order_items_count' => 'COALESCE(main_table.order_items_count, 0)',
                    'total_sales_percent' => 'COALESCE(main_table.total_sales_percent, 0)',
                    'total_sales' => 'COALESCE(main_table.total_sales, 0)',
                ]
            )
            ->group('all_ranges.range_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'sales_range') {
            $field = 'COALESCE(main_table.range_from, all_ranges.range_from)';
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * Retrieve chart rows
     *
     * @param array $additional
     * @return array
     */
    public function getChartRows($additional = [])
    {
        $chartSelect = clone $this->getSelect();
        $chartSelect
            ->reset(\Magento\Framework\DB\Select::ORDER)
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->order('COALESCE(all_ranges.range_from, 0) ' . self::SORT_ORDER_ASC);

        return $this->getConnection()->fetchAll($chartSelect) ?: [];
    }

    /**
     * Get select grouped by ranges
     *
     * @param \Zend_Db_Select $select
     * @return \Zend_Db_Select
     */
    private function getSelectByRange($select)
    {
        if ($this->totalSales === null) {
            $this->getTotals();
        }

        $rangesSelect = clone $this->getSelect();
        $rangesSelect
            ->reset()
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $select))],
                []
            )
            ->joinLeft(
                ['ranges' => $this->getTable('aw_arep_customer_sales_range')],
                'IF (ranges.range_to IS NULL, main_table.total_sales >= ranges.range_from,
                main_table.total_sales BETWEEN ranges.range_from AND ranges.range_to)
                AND ranges.website_id = ' . $this->websiteId,
                []
            )
            ->columns([
                'range_id' => 'ranges.range_id',
                'range_from' => 'ranges.range_from',
                'range_to' => 'ranges.range_to',
                'customers_count' => 'COUNT(main_table.customer_email)',
                'orders_count' => 'SUM(main_table.orders_count)',
                'order_items_count' => 'SUM(main_table.order_items_count)',
                'total_sales_percent' => 'COALESCE((100 / ' . $this->totalSales . ') * SUM(main_table.total_sales), 0)',
                'total_sales' => 'SUM(main_table.total_sales)',
            ])
            ->group('ranges.range_id')
        ;
        return $rangesSelect;
    }
}
