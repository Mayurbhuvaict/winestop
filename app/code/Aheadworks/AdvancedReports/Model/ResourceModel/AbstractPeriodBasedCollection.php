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

use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;
use Magento\Framework\DB\Select;

/**
 * Class AbstractPeriodBasedCollection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
abstract class AbstractPeriodBasedCollection extends AbstractCollection
{
    /**
     * @var string
     */
    const GROUP_TABLE_ALIAS = 'group_table';

    /**
     * @var bool
     */
    protected $addOrderStatusesToFilterInGroupBy = true;

    /**
     * @var array
     */
    protected $conditionsForGroupBy = [];

    /**
     * @var Select
     */
    private $notRenderedCollectionSelect;

    /**
     * Add group filter to collection
     *
     * @param string $groupByKey
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param \DateTime $compareFrom
     * @param \DateTime $compareTo
     * @return $this
     */
    public function addGroupByFilter($groupByKey, $periodFrom, $periodTo, $compareFrom, $compareTo)
    {
        $this->saveCustomFilterValue(self::GROUP_BY_FILTER_KEY, $groupByKey);
        $this->saveCustomFilterValue(self::PERIOD_FROM_FILTER_KEY, $periodFrom);
        $this->saveCustomFilterValue(self::PERIOD_TO_FILTER_KEY, $periodTo);
        $this->saveCustomFilterValue(self::COMPARE_PERIOD_FROM_FILTER_KEY, $compareFrom);
        $this->saveCustomFilterValue(self::COMPARE_PERIOD_TO_FILTER_KEY, $compareTo);

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
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.customer_group_id',
            'condition' => ['in' => $customerGroupsId]
        ];

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
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.store_id',
            'condition' => ['in' => $storeIds]
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'period') {
            switch ($this->getCustomFilterValue(self::GROUP_BY_FILTER_KEY)) {
                case GroupbySource::TYPE_DAY:
                    $field = 'date';
                    break;
                case GroupbySource::TYPE_WEEK:
                case GroupbySource::TYPE_MONTH:
                case GroupbySource::TYPE_QUARTER:
                case GroupbySource::TYPE_YEAR:
                    $field = 'start_date';
                    break;
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * Retrieve this month forecast totals
     *
     * @param string $periodFrom
     * @param string $periodTo
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    public function getItemsByCustomPeriod($periodFrom, $periodTo)
    {
        $collectionSelect = clone $this->notRenderedCollectionSelect;
        $collectionSelect = $this->renderPeriodFilterValues($collectionSelect, $periodFrom, $periodTo);
        $collectionSelect
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET);

        $items = $this->getConnection()->fetchAll($collectionSelect);

        return $items ?: [];
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->applyGroupByFilter();
        $this->notRenderedCollectionSelect = clone $this->getSelect();

        parent::_renderFiltersBefore();
    }

    /**
     * Add group by day to collection
     *
     * @return $this
     */
    protected function addGroupByDay()
    {
        $table = $this->getTable('aw_arep_days');
        $this->getSelect()->where(
            '(' . self::GROUP_TABLE_ALIAS . '.date BETWEEN "' . self::PERIOD_FROM_PLACEHOLDER . '" AND "'
            . self::PERIOD_TO_PLACEHOLDER . '")'
        );

        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                'period = ' . self::GROUP_TABLE_ALIAS . '.date'
                . $this->getOrderStatusesConditionForGroupByFilter()
            );
        $this->getSelect()->group(self::GROUP_TABLE_ALIAS . '.date');
        return $this;
    }

    /**
     * Add group by table to collection
     *
     * @param string $table
     * @return $this
     */
    protected function groupByTable($table)
    {
        $this->getSelect()
            ->where(
                self::GROUP_TABLE_ALIAS . '.start_date <= "' . self::PERIOD_TO_PLACEHOLDER . '"'
            )
            ->where(
                self::GROUP_TABLE_ALIAS . '.end_date >= "' . self::PERIOD_FROM_PLACEHOLDER . '"'
            );

        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                'period >= "' . self::PERIOD_FROM_PLACEHOLDER . '" AND '
                . 'period <= "' . self::PERIOD_TO_PLACEHOLDER . '"'
                . ' AND period BETWEEN group_table.start_date AND group_table.end_date '
                . $this->getOrderStatusesConditionForGroupByFilter()
            )
            ->group(self::GROUP_TABLE_ALIAS . '.start_date');
        return $this;
    }

    /**
     * Apply group by filter
     *
     * @return $this
     */
    protected function applyGroupByFilter()
    {
        switch ($this->getCustomFilterValue(self::GROUP_BY_FILTER_KEY)) {
            case GroupbySource::TYPE_DAY:
                $this->addGroupByDay();
                break;
            case GroupbySource::TYPE_WEEK:
                $table = $this->getTable('aw_arep_weeks');
                $this->groupByTable($table);
                break;
            case GroupbySource::TYPE_MONTH:
                $table = $this->getTable('aw_arep_month');
                $this->groupByTable($table);
                break;
            case GroupbySource::TYPE_QUARTER:
                $table = $this->getTable('aw_arep_quarter');
                $this->groupByTable($table);
                break;
            case GroupbySource::TYPE_YEAR:
                $table = $this->getTable('aw_arep_year');
                $this->groupByTable($table);
                break;
        }

        return $this;
    }

    /**
     * Retrieve order statuses condition for group by filter
     *
     * @return string
     */
    private function getOrderStatusesConditionForGroupByFilter()
    {
        return $this->addOrderStatusesToFilterInGroupBy
            ? ' AND ' . $this->_getConditionSql('main_table.order_status', ['in' => $this->getOrderStatuses()])
            : '';
    }

    /**
     * Get condition for group by day, week, month, quarter, year
     *
     * @return string
     */
    private function getConditionForGroupBy()
    {
        $joinCondition = '1=1';
        foreach ($this->conditionsForGroupBy as $condition) {
            $joinCondition .= ' AND ' . ($condition['condition']
                    ? $this->_getConditionSql($condition['field'], $condition['condition'])
                    : $condition['field']);
        }
        return $joinCondition;
    }
}
