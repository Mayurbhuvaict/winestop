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

use Aheadworks\AdvancedReports\Model\ResourceModel\Conversion as ResourceConversion;

/**
 * Class ProductConversion
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductConversion extends AbstractResource
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
        $this->_init('aw_arep_conversion_product', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $this->processOrders();
        $this->processViews();
    }

    /**
     * Process orders
     *
     * @return void
     */
    private function processOrders()
    {
        $columns = $this->getColumnsOrdered();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order_item')], [])
            ->columns($columns)
            ->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )
            ->where('main_table.parent_item_id IS NULL')
            ->group($this->getGroupByFieldsOrdered([]));

        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    /**
     * Retrieve columns for ordered table
     *
     * @return []
     */
    private function getColumnsOrdered()
    {
        $this->period = $this->getPeriod('main_table.created_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'customer_group_id' => 'order.customer_group_id',
            'product_id' => 'main_table.product_id',
            'product_name' => 'main_table.name',
            'orders_count' => 'COUNT(main_table.order_id)',
            'is_refunded' => 'IF((main_table.qty_ordered - main_table.qty_refunded) > 0, 0, 1)',
            'views_count' => 'COALESCE(0)',
        ];
        return $columns;
    }

    /**
     * Retrieve group by for ordered table
     *
     * @param [] $additionalFields
     * @return []
     */
    private function getGroupByFieldsOrdered($additionalFields)
    {
        return array_merge(
            [
                $this->period,
                'main_table.store_id',
                'order.status',
                'order.customer_group_id',
                'main_table.product_id',
                'is_refunded'
            ],
            $additionalFields
        );
    }

    /**
     * Process views
     *
     * @return void
     */
    private function processViews()
    {
        /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $this->attributeRepository->get('catalog_product', 'name');

        $columns = $this->getColumnsViewed();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('aw_arep_log_product_view')], [])
            ->joinLeft(
                ['product_entity' => $this->getTable('catalog_product_entity')],
                'product_entity.entity_id = main_table.product_id',
                []
            )
            ->joinLeft(
                ['catalog_product' => $this->getTable('catalog_product_entity_varchar')],
                'catalog_product.' . $this->getCatalogLinkField() . ' = product_entity.'
                . $this->getCatalogLinkField() . ' AND catalog_product.attribute_id = ' .
                $attribute->getId() . ' AND catalog_product.store_id = 0',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFieldsViewed([]));

        $select = $this->addFilterByLoggedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }

    /**
     * Retrieve columns for viewed data
     *
     * @return []
     */
    private function getColumnsViewed()
    {
        $this->period = $this->getPeriod('main_table.logged_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'main_table.store_id',
            'product_id' => 'main_table.product_id',
            'product_name' => 'IFNULL(catalog_product.value, CONCAT(main_table.product_id, " (product was deleted)"))',
            'order_status' => 'COALESCE(\'' . ResourceConversion::VIEWED_STATUS . '\')',
            'customer_group_id' => 'main_table.customer_group_id',
            'views_count' => 'COUNT(main_table.visitor_id)',
            'orders_count' => 'COALESCE(0)',
            'is_refunded' => 'COALESCE(0)',
        ];
        return $columns;
    }

    /**
     * Retrieve group by for viewed data
     *
     * @param [] $additionalFields
     * @return []
     */
    private function getGroupByFieldsViewed($additionalFields)
    {
        return array_merge(
            [
                $this->period,
                'main_table.store_id',
                'main_table.product_id',
                'main_table.customer_group_id'
            ],
            $additionalFields
        );
    }

    /**
     * Add filter by logged date
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $tableAlias
     * @return null
     */
    private function addFilterByLoggedAt($select, $tableAlias)
    {
        return $select->where($tableAlias . '.logged_at <= "' . $this->getUpdatedAtFlag() . '"');
    }
}
