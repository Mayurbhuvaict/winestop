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































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Mapper;

/**
 * Class Base
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Mapper
 */
class Base
{
    /**
     * Map row
     *
     * @param array $rowValues
     * @param array $numberColumns
     * @param bool $isCompare
     * @return array
     * @throws \Exception
     */
    public function mapRow($rowValues, $numberColumns, $isCompare)
    {
        $mappedRow = [];
        foreach ($rowValues as $index => $value) {
            $indexPrefix = $isCompare ? 'c_' : '';
            $mappedRow[$indexPrefix . $index] = $value;

            // add empty fields
            $indexPrefix = $isCompare ? '' : 'c_';
            $default = in_array($index, $numberColumns) ? 0 : $rowValues[$index];
            $mappedRow[$indexPrefix . $index] = $default;
        }

        return $mappedRow;
    }
}
