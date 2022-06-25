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

/**
 * Class AbandonedCarts
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class AbandonedCarts extends AbstractResource
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
        $this->_init('aw_arep_abandoned_carts', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();

        $select = $this->getConnection()->select()
            ->from(
                ['main_table' => new \Zend_Db_Expr(
                    "(SELECT quote.entity_id AS total_carts, NULL AS quote_completed, 
                        quote.entity_id as quote_abandoned, quote.base_grand_total AS abandoned_carts_total, 
                        IF(quote.created_at > quote.updated_at, quote.created_at, quote.updated_at) AS created_at, 
                        quote.store_id AS store_id, quote.customer_group_id AS customer_group_id, 
                        quote.base_to_global_rate AS base_to_global_rate
                        FROM " . $this->getTable('quote') . " AS quote
                        WHERE (quote.is_active = 1) AND (quote.items_count > 0) 
                    UNION
                    SELECT so.quote_id AS total_carts, so.quote_id as quote_completed, NULL AS quote_abandoned, 
                        0 AS abandoned_carts_total, so.created_at AS created_at, so.store_id AS store_id,
                        so.customer_group_id AS customer_group_id, so.base_to_global_rate AS base_to_global_rate
                        FROM " . $this->getTable('sales_order') . " AS so
                )"
                )],
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields([]));
        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    /**
     * Retrieve base columns for table
     *
     * @return []
     */
    protected function getColumns()
    {
        $this->period = $this->getPeriod('main_table.created_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'main_table.store_id',
            'customer_group_id' => 'main_table.customer_group_id',
            'total_carts' => "COALESCE(COUNT(total_carts), 0)",
            'completed_carts' => "COALESCE(COUNT(quote_completed), 0)",
            'abandoned_carts' => "COALESCE(COUNT(quote_abandoned), 0)",
            'abandoned_carts_total' => "COALESCE(SUM(abandoned_carts_total), 0)",
            'to_global_rate' => 'main_table.base_to_global_rate'
        ];
        return $columns;
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
                $this->period,
                'main_table.store_id',
                'main_table.base_to_global_rate',
                'main_table.customer_group_id'
            ],
            $additionalFields
        );
    }
}
