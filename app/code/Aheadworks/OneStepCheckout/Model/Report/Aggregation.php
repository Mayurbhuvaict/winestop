<?php
namespace Aheadworks\OneStepCheckout\Model\Report;

/**
 * Class Aggregation
 * @package Aheadworks\OneStepCheckout\Model\Report
 */
class Aggregation
{
    /**
     * Get aggregations
     *
     * @return array
     */
    public function getAggregations()
    {
        return ['day', 'week', 'month', 'quarter', 'year'];
    }
}
