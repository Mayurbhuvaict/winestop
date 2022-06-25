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



































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider;

/**
 * Interface MetadataInterface
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
interface MetadataInterface
{
    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Get default filter appliers
     *
     * @return array
     */
    public function getDefaultFilterAppliers();

    /**
     * Get individual filter appliers
     *
     * @return array
     */
    public function getIndividualFilterAppliers();

    /**
     * Get compare rows merger
     *
     * @return string
     */
    public function getCompareRowsMerger();

    /**
     * Get compare rows merger config
     *
     * @return array
     */
    public function getCompareRowsMergerConfig();

    /**
     * Get compare chart rows merger
     *
     * @return string
     */
    public function getCompareChartsMerger();

    /**
     * Get compare chart rows merger config
     *
     * @return array
     */
    public function getCompareChartsMergerConfig();

    /**
     * Get compare totals merger
     *
     * @return string
     */
    public function getCompareTotalsMerger();
}
