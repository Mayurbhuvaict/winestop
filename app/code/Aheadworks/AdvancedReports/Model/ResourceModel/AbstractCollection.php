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













































































namespace Aheadworks\AdvancedReports\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;
use Magento\Framework\DB\Select;

/**
 * Class AbstractCollection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
abstract class AbstractCollection extends DbAbstractCollection
{
    /**#@+
     * Period filter placeholders
     */
    const PERIOD_FROM_PLACEHOLDER = '%PERIOD_FROM%';
    const PERIOD_TO_PLACEHOLDER = '%PERIOD_TO%';
    /**#@-*/

    /**#@+
     * Custom filters key
     */
    const GROUP_BY_FILTER_KEY = 'group_by';
    const ORDER_STATUSES_FILTER_KEY = 'order_statuses';
    const STORE_IDS_FILTER_KEY = 'store_ids';
    const PERIOD_FROM_FILTER_KEY = 'period_from';
    const PERIOD_TO_FILTER_KEY = 'period_to';
    const COMPARE_PERIOD_FROM_FILTER_KEY = 'compare_period_from';
    const COMPARE_PERIOD_TO_FILTER_KEY = 'compare_period_to';
    /**#@-*/

    /**
     * @var AbstractCollection
     */
    protected $collectionSelect;

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var array
     */
    protected $customFiltersCache = [];

    /**
     * @var bool
     */
    private $compareMode = false;

    /**
     * Set order statuses
     *
     * @param array $orderStatuses
     * @return $this
     */
    public function setOrderStatuses(array $orderStatuses)
    {
        $this->saveCustomFilterValue(self::ORDER_STATUSES_FILTER_KEY, $orderStatuses);

        return $this;
    }

    /**
     * Add period filter to collection
     *
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param \DateTime $compareFrom
     * @param \DateTime $compareTo
     * @return $this
     */
    public function addPeriodFilter($periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        $this->saveCustomFilterValue(self::PERIOD_FROM_FILTER_KEY, $periodFrom);
        $this->saveCustomFilterValue(self::PERIOD_TO_FILTER_KEY, $periodTo);
        $this->saveCustomFilterValue(self::COMPARE_PERIOD_FROM_FILTER_KEY, $compareFrom);
        $this->saveCustomFilterValue(self::COMPARE_PERIOD_TO_FILTER_KEY, $compareTo);

        $this
            ->addFieldToFilter('period', [
                'from' => self::PERIOD_FROM_PLACEHOLDER,
                'to' => self::PERIOD_TO_PLACEHOLDER
            ])
            ->addOrderStatusFilter();

        return $this;
    }

    /**
     * Add order statuses filter to collection
     *
     * @return $this
     */
    protected function addOrderStatusFilter()
    {
        $this->addFieldToFilter('order_status', ['in' => $this->getOrderStatuses()]);
        return $this;
    }

    /**
     * Add customer group filter to collection
     *
     * @param int $customerGroupsId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupsId)
    {
        $this->addFieldToFilter('customer_group_id', ['in' => $customerGroupsId]);

        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->saveCustomFilterValue(self::STORE_IDS_FILTER_KEY, $storeIds);
        $this->addFieldToFilter('store_id', ['in' => $storeIds]);

        return $this;
    }

    /**
     * Add filter to collection for chart
     *
     * @return $this
     */
    public function addFilterForChart()
    {
        // no implementation, should be overridden in children classes

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, ['store_id', 'customer_group_id', 'order_status', 'period'])) {
            return parent::addFieldToFilter($field, $condition);
        }

        // Apply filters for grid query
        return $this->addFilter($field, $condition, 'public');
    }

    /**
     * Enable compare mode
     *
     * @return $this
     */
    public function enableCompareMode()
    {
        $this->compareMode = true;

        return $this;
    }

    /**
     * Retrieve totals
     *
     * @return array
     */
    public function getTotals()
    {
        $collectionSelect = clone $this->collectionSelect->getSelect();
        $collectionSelect->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET);

        $totalSelect = clone $this->getSelect();
        $totalSelect->reset(Select::COLUMNS)
            ->reset(Select::FROM)
            ->reset(Select::GROUP)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect))],
                $this->getTotalColumns()
            );
        $totals = $this->getConnection()->fetchRow($totalSelect);

        return $totals ?: [];
    }

    /**
     * Retrieve chart rows
     *
     * @return array
     */
    public function getChartRows()
    {
        $chartSelect = $this->getChartQuery();
        $chartItems = $this->getConnection()->fetchAll($chartSelect);

        return $chartItems ?: [];
    }

    /**
     * Retrieve top chart rows
     *
     * @param string $topByColumn
     * @param string $compareColumn
     * @param array $compareEntityIds
     * @return array
     */
    public function getTopChartRows($topByColumn, $compareColumn = null, $compareEntityIds = [])
    {
        $chartSelect = $this->getChartQuery()
            ->limit(10)
            ->order($topByColumn . ' ' . self::SORT_ORDER_DESC);

        if ($this->compareMode) {
            $chartSelect->where($compareColumn . ' in(?)', $compareEntityIds);
        }

        $chartItems = $this->getConnection()->fetchAll($chartSelect);
        return $chartItems ?: [];
    }

    /**
     * Get custom filter value
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getCustomFilterValue($key, $default = null)
    {
        if (isset($this->customFiltersCache[$key])) {
            return $this->customFiltersCache[$key];
        };

        return $default;
    }

    /**
     * Retrieve report columns
     *
     * @param boolean $addRate
     * @return array
     */
    abstract protected function getColumns($addRate = false);

    /**
     * Retrieve report total columns
     *
     * @param boolean $addRate
     * @return array
     */
    protected function getTotalColumns($addRate = false)
    {
        return $this->getColumns($addRate);
    }

    /**
     * Retrieve chart query
     *
     * @return Select
     */
    protected function getChartQuery()
    {
        $collectionSelect = clone $this->collectionSelect;

        $collectionSelect->addFilterForChart();
        $collectionSelect->getSelect()
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $chartSelect = clone $this->getSelect();
        $chartSelect->reset(Select::COLUMNS)
            ->reset(Select::FROM)
            ->reset(Select::GROUP)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->reset(Select::ORDER)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect->getSelect()))],
                ['*']
            );

        return $chartSelect;
    }

    /**
     * Save custom filter value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function saveCustomFilterValue($key, $value)
    {
        $this->customFiltersCache[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->collectionSelect = clone $this;
        $this->renderSelect($this->collectionSelect->getSelect());

        // Change select for apply grid filters
        $this->getSelect()->reset()->from(
            ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $this->collectionSelect->getSelect()))],
            ['*']
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Change main table
     *
     * @param string $suffix
     * @return $this
     */
    protected function changeMainTable($suffix)
    {
        $this->setMainTable($this->getMainTable() . $suffix);
        return $this;
    }

    /**
     * Retrieve rate field if necessary
     *
     * @param boolean $addRate
     * @return string
     */
    protected function getRateField($addRate = true)
    {
        return (empty($this->getCustomFilterValue(self::STORE_IDS_FILTER_KEY)) && $addRate)
            ? ' * main_table.to_global_rate'
            : '';
    }

    /**
     * Retrieve order statuses
     *
     * @return array
     */
    protected function getOrderStatuses()
    {
        $orderStatuses = $this->getCustomFilterValue(self::ORDER_STATUSES_FILTER_KEY);

        return is_array($orderStatuses) ? $orderStatuses : [];
    }

    /**
     * Render select
     *
     * @param Select $select
     * @return Select
     * @throws \Zend_Db_Select_Exception
     */
    protected function renderSelect($select)
    {
        if ($this->compareMode &&
            !empty($this->getCustomFilterValue(self::COMPARE_PERIOD_FROM_FILTER_KEY)) &&
            !empty($this->getCustomFilterValue(self::COMPARE_PERIOD_TO_FILTER_KEY))
        ) {
            $this->renderPeriodFilterValues(
                $select,
                $this->getCustomFilterValue(self::COMPARE_PERIOD_FROM_FILTER_KEY)->format('Y-m-d'),
                $this->getCustomFilterValue(self::COMPARE_PERIOD_TO_FILTER_KEY)->format('Y-m-d')
            );
        }

        if (!empty($this->getCustomFilterValue(self::PERIOD_FROM_FILTER_KEY)) &&
            !empty($this->getCustomFilterValue(self::PERIOD_TO_FILTER_KEY))
        ) {
            $this->renderPeriodFilterValues(
                $select,
                $this->getCustomFilterValue(self::PERIOD_FROM_FILTER_KEY)->format('Y-m-d'),
                $this->getCustomFilterValue(self::PERIOD_TO_FILTER_KEY)->format('Y-m-d')
            );
        }
        return $select;
    }

    /**
     * Render period filter values
     *
     * @param Select $select
     * @param string $periodFrom
     * @param string $periodTo
     * @return Select
     * @throws \Zend_Db_Select_Exception
     */
    protected function renderPeriodFilterValues($select, $periodFrom, $periodTo)
    {
        $where = $select->getPart(Select::WHERE);
        foreach ($where as $key => $value) {
            $where[$key] = str_replace(
                self::PERIOD_FROM_PLACEHOLDER,
                $periodFrom,
                str_replace(
                    self::PERIOD_TO_PLACEHOLDER,
                    $periodTo,
                    $value
                )
            );
        }
        $select->setPart(Select::WHERE, $where);

        $from = $select->getPart(Select::FROM);
        foreach ($from as $key => $value) {
            if (isset($value['joinCondition']) && $value['joinCondition']) {
                $from[$key]['joinCondition'] = str_replace(
                    self::PERIOD_FROM_PLACEHOLDER,
                    $periodFrom,
                    str_replace(
                        self::PERIOD_TO_PLACEHOLDER,
                        $periodTo,
                        $value['joinCondition']
                    )
                );
            }
        }
        $select->setPart(Select::FROM, $from);

        return $select;
    }
}
