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
 * Class Location
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Location extends SalesOverview
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_location', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['country_id'] = 'COALESCE(order_address_ship.country_id, order_address_bill.country_id)';
        $columns['region'] = 'COALESCE(order_address_ship.region, order_address_bill.region)';
        $columns['city'] = 'COALESCE(order_address_ship.city, order_address_bill.city)';

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order')], [])
            ->joinLeft(
                ['order_address_bill' => $this->getTable('sales_order_address')],
                'order_address_bill.entity_id = main_table.billing_address_id',
                []
            )
            ->joinLeft(
                ['order_address_ship' => $this->getTable('sales_order_address')],
                'order_address_ship.entity_id = main_table.shipping_address_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields(['country_id', 'region', 'city']));
        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
