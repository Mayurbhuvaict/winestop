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




























































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts as ResourceAbandonedCarts;
use Aheadworks\AdvancedReports\Model\ResourceModel\AbstractPeriodBasedCollection;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\AbandonedCarts
 */
class Collection extends AbstractPeriodBasedCollection
{
    /**
     * @var bool
     */
    protected $addOrderStatusesToFilterInGroupBy = false;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceAbandonedCarts::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], []);
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
        $totalCarts = "SUM(COALESCE(main_table.total_carts, 0))";
        $abandonedCarts = "SUM(COALESCE(main_table.abandoned_carts, 0))";
        $rateField = $this->getRateField($addRate);
        return [
            'period'                => 'period',
            'total_carts'           => $totalCarts,
            'completed_carts'       => "SUM(COALESCE(main_table.completed_carts, 0))",
            'abandoned_carts'       => $abandonedCarts,
            'abandoned_carts_total' => "SUM(COALESCE(main_table.abandoned_carts_total" . $rateField . ", 0))",
            'abandonment_rate'      => "COALESCE((100 / " . $totalCarts . ") * " . $abandonedCarts . ", 0)",
        ];
    }
}
