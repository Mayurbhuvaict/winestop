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


























































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor;

/**
 * Class Totals
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor
 */
class Totals implements MergerInterface
{
    /**
     * {@inheritdoc}
     */
    public function merge($rows, $compareRows, $dataSourceData)
    {
        $rows = $this->mergeArray($rows, $compareRows);

        return $rows;
    }

    /**
     * Merge data from two arrays
     *
     * @param array $rows
     * @param array $compareRows
     * @return array
     */
    private function mergeArray($rows, $compareRows)
    {
        foreach ($compareRows as $compareRowIndex => $compareRowValue) {
            foreach ($compareRowValue as $index => $value) {
                $rows[$compareRowIndex]['c_' . $index] = $value;
            }
        }

        return $rows;
    }
}
