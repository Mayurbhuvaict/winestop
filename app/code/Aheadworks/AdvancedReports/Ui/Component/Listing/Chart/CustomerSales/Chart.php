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











































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Chart\CustomerSales;

/**
 * Class Chart
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Chart
 */
class Chart extends \Aheadworks\AdvancedReports\Ui\Component\Listing\Chart\Chart
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        $dataSource['data']['chart']['rows'] = $this->formatChartRows(
            $dataSource['data']['chart']['rows'],
            $dataSource['data']['priceFormat']
        );

        return $dataSource;
    }

    /**
     * Format chart rows
     *
     * @param array $rows
     * @param array $priceFormat
     * @return array
     */
    private function formatChartRows($rows, $priceFormat)
    {
        $formattedRows = [];
        foreach ($rows as $row) {
            $row['sales_range'] = $this->formatChartRange($row['range_from'], $row['range_to'], $priceFormat);
            $formattedRows[] = $row;
        }
        return $formattedRows;
    }

    /**
     * Format chart range
     *
     * @param int $from
     * @param int $to
     * @param array $format
     * @return \Magento\Framework\Phrase
     */
    private function formatChartRange($from, $to, $format)
    {
        $rangeFrom = number_format(
            $from,
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $rangeTo = number_format(
            $to,
            $format['precision'],
            $format['decimalSymbol'],
            $format['groupSymbol']
        );
        $fromPattern = str_replace('%s', '%1', $format['pattern']);
        $toPattern = str_replace('%s', '%2', $format['pattern']);
        if ($to) {
            $resultRange = __($fromPattern . ' - ' . $toPattern, [$rangeFrom, $rangeTo]);
        } else {
            $resultRange = __($fromPattern . '+', [$rangeFrom]);
        }

        return $resultRange;
    }
}
