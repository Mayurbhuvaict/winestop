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
 * Class ProductPerformanceByCategory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformanceByCategory extends ProductPerformance
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['category_id'] = 'category.category_id';

        $select =
            $this->joinParentItems()
            ->join(
                ['category' => $this->getTable('catalog_category_product')],
                'main_table.product_id = category.product_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields(['category.category_id']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
