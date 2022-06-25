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




























































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class FilterPool
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class FilterPool
{
    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(
        array $filters = []
    ) {
        $this->filters = $filters;
    }

    /**
     * Retrieve filter by filter name
     *
     * @param string $filterName
     * @return FilterInterface|null
     */
    public function getFilter($filterName)
    {
        if (isset($this->filters[$filterName])) {
            return $this->filters[$filterName];
        }

        return null;
    }
}
