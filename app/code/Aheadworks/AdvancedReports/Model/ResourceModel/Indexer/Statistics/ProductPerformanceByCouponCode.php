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
 * Class ProductPerformanceByCouponCode
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformanceByCouponCode extends ProductPerformance
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance_coupon_code', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['coupon_code'] = 'order.coupon_code';

        $select =
            $this->joinParentItems()
            ->columns($columns)
            ->where('order.coupon_code IS NOT NULL')
            ->group($this->getGroupByFields(['order.coupon_code']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
