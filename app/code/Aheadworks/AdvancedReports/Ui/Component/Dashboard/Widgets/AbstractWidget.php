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












































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Dashboard\Widgets;

use Aheadworks\AdvancedReports\Ui\Component\OptionsContainer;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface;

/**
 * Class AbstractWidget
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Dashboard\Widgets
 */
abstract class AbstractWidget extends OptionsContainer
{
    /**
     * @var array
     */
    protected $reportCustomizedColumns;

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareReportCustomizedColumns();
        $this->addOptions();

        parent::prepare();
    }

    /**
     * Add options
     *
     * @return $this
     */
    protected function addOptions()
    {
        $config = $this->getData('config');
        $config['options'] = $this->getMetricOptions();
        $this->setData('config', $config);

        return $this;
    }

    /**
     * Retrieve metric options
     *
     * @return array
     */
    abstract protected function getMetricOptions();

    /**
     * Retrieve Sales report metric options
     *
     * @return array
     */
    protected function getSalesReportMetrics()
    {
        return [
            [
                'value' => 'sales',
                'label' => __('Sales'),
                'additionalClasses' => ['not-active' => true],
                'url' => $this->getReportBaseUrl('salesoverview'),
                'chartConfig' => [
                    'xAxis' => 'period'
                ]
            ],
            [
                'value' => 'sales.total',
                'label' => $this->getColumnLabel('sales', 'total', 'Total'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.orders_count',
                'label' => $this->getColumnLabel('sales', 'orders_count', 'Number of Orders'),
            ],
            [
                'value' => 'sales.discount',
                'label' => $this->getColumnLabel('sales', 'discount', 'Discounts'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.invoiced',
                'label' => $this->getColumnLabel('sales', 'invoiced', 'Invoiced'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.refunded',
                'label' => $this->getColumnLabel('sales', 'refunded', 'Refunded'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.order_items_count',
                'label' => $this->getColumnLabel('sales', 'order_items_count', 'Items Ordered'),
            ],
            [
                'value' => 'sales.avg_order_amount',
                'label' => $this->getColumnLabel('sales', 'avg_order_amount', 'Avg. Order Value'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.tax',
                'label' => $this->getColumnLabel('sales', 'tax', 'Tax'),
                'columnType' => 'price'
            ]
        ];
    }

    /**
     * Retrieve forecast Sales report metric options
     *
     * @return array
     */
    protected function getForecastSalesReportMetrics()
    {
        return [
            [
                'value' => 'sales',
                'label' => __('Forecast: Sales'),
                'additionalClasses' => ['not-active' => true]
            ],
            [
                'value' => 'sales.total',
                'label' => __('Forecast: Total'),
                'columnType' => 'price'
            ],
            [
                'value' => 'sales.order_items_count',
                'label' => __('Forecast: Items Ordered')
            ]
        ];
    }

    /**
     * Retrieve Traffic and Conversions report metric options
     *
     * @return array
     */
    protected function getTrafficAndConversionsReportMetrics()
    {
        return [
            [
                'value' => 'traffic_and_conversions',
                'label' => __('Traffic and Conversions'),
                'additionalClasses' => ['not-active' => true],
                'url' => $this->getReportBaseUrl('conversion'),
                'chartConfig' => [
                    'xAxis' => 'period'
                ]
            ],
            [
                'value' => 'traffic_and_conversions.views_count',
                'label' => $this->getColumnLabel('traffic_and_conversions', 'views_count', 'Unique Visitors'),
            ],
            [
                'value' => 'traffic_and_conversions.conversion_rate',
                'label' => $this->getColumnLabel('traffic_and_conversions', 'conversion_rate', 'Conversion Rate, %'),
                'columnType' => 'percent'
            ]
        ];
    }

    /**
     * Retrieve Abandoned Carts report metric options
     *
     * @return array
     */
    protected function getAbandonedCartsReportMetrics()
    {
        return [
            [
                'value' => 'abandoned_carts',
                'label' => __('Abandoned Carts'),
                'additionalClasses' => ['not-active' => true],
                'url' => $this->getReportBaseUrl('abandonedcarts'),
                'chartConfig' => [
                    'xAxis' => 'period'
                ]
            ],
            [
                'value' => 'abandoned_carts.abandoned_carts',
                'label' => $this->getColumnLabel('abandoned_carts', 'abandoned_carts', 'Abandoned Carts'),
            ],
            [
                'value' => 'abandoned_carts.abandoned_carts_total',
                'label' => $this->getColumnLabel('abandoned_carts', 'abandoned_carts_total', 'Abandoned Carts Total'),
                'columnType' => 'price'
            ],
            [
                'value' => 'abandoned_carts.abandonment_rate',
                'label' => $this->getColumnLabel('abandoned_carts', 'abandonment_rate', 'Abandonment Rate'),
                'columnType' => 'percent'
            ]
        ];
    }

    /**
     * Retrieve report url from dashboard
     *
     * @param string $reportName
     * @return string
     */
    protected function getReportBaseUrl($reportName)
    {
        $filterPool = $this->context->getDataProvider()->getDefaultFilterPool();
        $customerGroupFilter = $filterPool->getFilter('customer_group');
        $periodFilter = $filterPool->getFilter('period');
        $storeFilter = $filterPool->getFilter('store');

        $params = [
            '_query' => [
                'customer_group_id' => $customerGroupFilter->getDefaultValue(),
                'period_type' => $periodFilter->getDefaultPeriodType(),
                'report_scope' => $storeFilter->getDefaultValue(),
                'compare_type' => $periodFilter->getDefaultCompareType()
            ]
        ];
        $url = $this->getContext()->getUrl('advancedreports/' . $reportName . '/index', $params);

        return $url;
    }

    /**
     * Prepare customized columns
     */
    protected function prepareReportCustomizedColumns()
    {
        $data = $this->context->getDataProvider()->getData();
        $reports = $data['reports'] ?? [];
        foreach ($reports as $reportName => $report) {
            $columnsCustomization = $report['report_configuration']['columns_customization'];
            foreach ($columnsCustomization as $customizedColumn) {
                $this->reportCustomizedColumns[$reportName][$customizedColumn['column_name']] = $customizedColumn;
            }
        }
    }

    /**
     * Prepare column name
     *
     * @param string $report
     * @param string $columnName
     * @param string $defaultName
     * @return string
     */
    protected function getColumnLabel($report, $columnName, $defaultName)
    {
        $columnsList = $this->reportCustomizedColumns[$report] ?? null;
        $result = $defaultName;
        if ($columnsList) {
            $result = isset($columnsList[$columnName])
                && $columnsList[$columnName][ColumnCustomizationInterface::CUSTOM_LABEL]
                ? $columnsList[$columnName][ColumnCustomizationInterface::CUSTOM_LABEL]
                : $defaultName;
        }

        return __($result);
    }
}
