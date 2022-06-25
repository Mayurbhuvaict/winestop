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








































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Chart;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Manager as CompareMergerManager;

/**
 * Class Chart
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Component\Listing\Chart
 */
class Chart extends Container
{
    /**
     * @var CompareMergerManager
     */
    private $mergerManager;

    /**
     * @param ContextInterface $context
     * @param CompareMergerManager $mergerManager
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        CompareMergerManager $mergerManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->mergerManager = $mergerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $compareEnabled = $dataSource['data']['compareEnabled'] && !$this->isCompareDisabledForChart();
        $rows = $this->getChartRows();

        if ($compareEnabled) {
            $compareRows = $this->getCompareChartRows($rows);

            $dataSource['data']['chart']['compare_rows'] = $compareRows;
            $dataSource['data']['chart']['rows'] = $rows;

            $dataSourceName = $this->context->getDataProvider()->getName();
            $rows = $this->mergerManager->mergeChartRows($dataSourceName, $dataSource['data']);

            unset($dataSource['data']['chart']['compare_rows']);
        }
        $dataSource['data']['chart']['rows'] = $rows;

        return parent::prepareDataSource($dataSource);
    }

    /**
     * Retrive chart rows
     *
     * @return array
     */
    protected function getChartRows()
    {
        $searchResult = $this->getContext()->getDataProvider()->getSearchResultCached();
        $topChartColumn = $this->getTopChartColumn();

        $rows = $this->isTopChart()
            ? $searchResult->getTopChartRows($topChartColumn)
            : $searchResult->getChartRows();

        return $rows;
    }

    /**
     * Retrieve compare chart rows
     *
     * @param array $rows
     * @return array
     */
    protected function getCompareChartRows($rows)
    {
        $compareSearchResult = $this->getContext()->getDataProvider()->getCompareSearchResultCached();
        $topChartColumn = $this->getTopChartColumn();
        $topChartCompareColumn = $this->getTopChartCompareColumn();

        $entityIds = $this->isTopChart()
            ? $this->getCompareEntityIdsForTopChart($rows, $topChartCompareColumn)
            : [];

        $compareRows = $this->isTopChart()
            ? $compareSearchResult->getTopChartRows($topChartColumn, $topChartCompareColumn, $entityIds)
            : $compareSearchResult->getChartRows();

        return $compareRows;
    }

    /**
     * Check is compare disabled for chart
     *
     * @return bool
     */
    protected function isCompareDisabledForChart()
    {
        return $this->getData('config/compareDisabled') ?: false;
    }

    /**
     * Check is top chart
     *
     * @return bool
     */
    protected function isTopChart()
    {
        return $this->getData('config/topChartConfig') ?: false;
    }

    /**
     * Retrieve top chart column
     *
     * @return string
     */
    protected function getTopChartColumn()
    {
        return $this->getData('config/topChartConfig/topByColumn') ?: '';
    }

    /**
     * Retrieve top chart compare column
     *
     * @return string
     */
    protected function getTopChartCompareColumn()
    {
        return $this->getData('config/topChartConfig/compareColumn') ?: '';
    }

    /**
     * Retrieve compare entity ids for top chart
     *
     * @param array $rows
     * @param string $compareColumn
     * @return array
     */
    private function getCompareEntityIdsForTopChart($rows, $compareColumn)
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $row[$compareColumn];

            if (count($result) == 10) {
                break;
            }
        }

        return $result;
    }
}
