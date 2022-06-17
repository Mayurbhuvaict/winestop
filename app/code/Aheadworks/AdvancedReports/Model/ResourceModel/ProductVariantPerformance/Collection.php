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

























































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance;

use Aheadworks\AdvancedReports\Model\ResourceModel\AbstractPeriodBasedCollection;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance as ResourceProductVariantPerformance;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance
 */
class Collection extends AbstractPeriodBasedCollection
{
    /**
     * @var bool
     */
    private $isApplyGroupByFilter = false;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductVariantPerformance::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->group(['product_id', 'sku']);

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
        $rateField = $this->getRateField($addRate);
        return [
                'product_id' => 'product_id',
                'product_name' => 'product_name',
                'sku' => 'sku',
                'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
                'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
                'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
                'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
                'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
                'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
                'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))'
            ];
    }

    /**
     * {@inheritdoc}
     */
    protected function applyGroupByFilter()
    {
        if ($this->isApplyGroupByFilter) {
            return parent::applyGroupByFilter();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'sku') {
            return $this->addSkuFilter($condition);
        }
        if ($field == 'product_name') {
            return $this->addProductNameFilter($condition);
        }
        if ($field == 'product_id') {
            $condition = $condition['eq'];
            return $this->addProductFilter($condition['product_id'], $condition['parent_id']);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add product id filter to collection
     *
     * @param int $productId
     * @param int|null $parentProductId
     * @return $this
     */
    public function addProductFilter($productId, $parentProductId = null)
    {
        if ($parentProductId) {
            $query = '(main_table.product_id = ' . $productId . ' AND main_table.parent_id IS NOT NULL)';
            $this->getSelect()->where($query);
        } else {
            $query = '(main_table.parent_product_id = ' . $productId . ' OR (main_table.product_id = '
                . $productId . ' AND main_table.parent_id = 0))';
            $this->getSelect()->where($query);
        }

        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];
        return $this;
    }

    /**
     * Add product sku filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addSkuFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.sku',
            'condition' => $condition
        ];
        $resultCondition = $this->_getConditionSql('sku', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add product name filter to collection
     *
     * @param array $condition
     * @return $this
     */
    public function addProductNameFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.product_name',
            'condition' => $condition
        ];
        $resultCondition = $this->_getConditionSql('product_name', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerGroupFilter($customerGroupsId)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.customer_group_id',
            'condition' => ['in' => $customerGroupsId]
        ];
        $this->addFieldToFilter('customer_group_id', ['in' => $customerGroupsId]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreFilter($storeIds)
    {
        $this->saveCustomFilterValue(self::STORE_IDS_FILTER_KEY, $storeIds);
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.store_id',
            'condition' => ['in' => $storeIds]
        ];
        $this->addFieldToFilter('store_id', ['in' => $storeIds]);

        return $this;
    }

    /**
     * Add group by filter for chart
     *
     * @param int $groupBy
     * @return $this
     */
    public function addGroupByFilterForChart($groupBy)
    {
        $this->saveCustomFilterValue(self::GROUP_BY_FILTER_KEY, $groupBy);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilterForChart()
    {
        $this->getSelect()
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::WHERE);
        $this->isApplyGroupByFilter = true;
        $this->applyGroupByFilter();
        $this->isApplyGroupByFilter = false;

        $this->renderSelect($this->getSelect());
        return $this;
    }
}
