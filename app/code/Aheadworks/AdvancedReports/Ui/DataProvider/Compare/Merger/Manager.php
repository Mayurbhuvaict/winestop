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























































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger;

use Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor\Pool as MergerPool;
use Aheadworks\AdvancedReports\Ui\DataProvider\MetadataInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\MetadataPool as DataProviderMetadataPool;

/**
 * Class Manager
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger
 */
class Manager
{
    /**
     * @var MergerPool
     */
    private $mergerPool;

    /**
     * @var DataProviderMetadataPool
     */
    private $dataProviderMetadataPool;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @param MergerPool $mergerPool
     * @param DataProviderMetadataPool $dataProviderMetadataPool
     * @param Calculator calculator
     */
    public function __construct(
        MergerPool $mergerPool,
        DataProviderMetadataPool $dataProviderMetadataPool,
        Calculator $calculator
    ) {
        $this->mergerPool = $mergerPool;
        $this->dataProviderMetadataPool = $dataProviderMetadataPool;
        $this->calculator = $calculator;
    }

    /**
     * Merge chart rows
     *
     * @param string $dataSourceName
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function mergeChartRows($dataSourceName, $data)
    {
        $metadata = $this->dataProviderMetadataPool->getMetadata($dataSourceName);
        $config = $this->resolveMetadataChartConfig($metadata) ? : [];
        $merger = $this->mergerPool->getMerger($metadata->getCompareChartsMerger(), $config);

        $chartRows = $data['chart'];
        $rows = $merger->merge($chartRows['rows'], $chartRows['compare_rows'], $data);

        return $rows;
    }

    /**
     * Merge items
     *
     * @param string $dataSourceName
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function mergeItems($dataSourceName, $data)
    {
        $metadata = $this->dataProviderMetadataPool->getMetadata($dataSourceName);
        $config = $metadata->getCompareRowsMergerConfig() ? : [];
        $merger = $this->mergerPool->getMerger($metadata->getCompareRowsMerger(), $config);

        $items = $merger->merge($data['items'], $data['compare_items'], $data);
        $items = $this->calculator->calculate($items, $data['number_columns']);

        return $items;
    }

    /**
     * Merge totals
     *
     * @param string $dataSourceName
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function mergeTotals($dataSourceName, $data)
    {
        $metadata = $this->dataProviderMetadataPool->getMetadata($dataSourceName);
        $merger = $this->mergerPool->getMerger($metadata->getCompareTotalsMerger());

        $totals = $merger->merge($data['totals'], $data['compare_totals'], $data);
        $totals = $this->calculator->calculate($totals, $data['number_columns']);

        return reset($totals);
    }

    /**
     * Resolve metadata chart config
     *
     * @param MetadataInterface $metadata
     * @return array|null
     */
    private function resolveMetadataChartConfig($metadata)
    {
        return $metadata->getCompareChartsMergerConfig() === null
            ? $metadata->getCompareRowsMergerConfig()
            : $metadata->getCompareChartsMergerConfig();
    }
}
