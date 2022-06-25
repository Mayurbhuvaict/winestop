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

/**
 * Class Calculator
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger
 */
class Calculator
{
    /**
     * Calculate
     *
     * @param array $rows
     * @param array $numberColumns
     * @return array
     * @throws \Exception
     */
    public function calculate($rows, $numberColumns)
    {
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $index => $value) {
                if ($this->isCompareIndex($index)) {
                    continue;
                }
                if (!in_array($index, $numberColumns)) {
                    continue;
                }
                $compareValue = isset($row['c_' . $index]) ? $row['c_' . $index] : 0;

                $rows[$rowIndex]['diff_value_' . $index] = $this->getDiffValue($value, $compareValue);
                $rows[$rowIndex]['diff_percent_' . $index] = $this->getDiffPercentValue($value, $compareValue);
            }
        }

        return $rows;
    }

    /**
     * Check if undex compare
     *
     * @param $index
     * @return bool
     */
    private function isCompareIndex($index)
    {
        return substr($index, 0, 2) == 'c_';
    }

    /**
     * Retrieve difference value
     *
     * @param string $value
     * @param string $compareValue
     * @return float
     */
    private function getDiffValue($value, $compareValue)
    {
        return (float)$value - (float)$compareValue;
    }

    /**
     * Retrieve difference value in percent
     *
     * @param string $value
     * @param string $compareValue
     * @return float
     */
    private function getDiffPercentValue($value, $compareValue)
    {
        if ($compareValue == 0 && $value == 0) {
            return 0;
        }
        if ($compareValue == 0) {
            return 100;
        }

        return (($value - $compareValue) / $compareValue) * 100;
    }
}
