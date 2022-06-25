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





















































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\GroupBy;

use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\FilterApplierInterface;

/**
 * Class Applier
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\GroupBy
 */
class Applier implements FilterApplierInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply($collection, $filterPool)
    {
        $periodFilter = $filterPool->getFilter('period');

        $groupBy = $filterPool->getFilter('group_by')->getValue();
        $periodFrom = $periodFilter->getPeriodFrom();
        $periodTo = $periodFilter->getPeriodTo();
        $compareFrom = $periodFilter->getCompareFrom();
        $compareTo = $periodFilter->getCompareTo();

        $collection->addGroupByFilter($groupBy, $periodFrom, $periodTo, $compareFrom, $compareTo);
    }
}
