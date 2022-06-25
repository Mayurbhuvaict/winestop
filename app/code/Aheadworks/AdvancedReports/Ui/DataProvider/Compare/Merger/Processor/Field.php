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

use Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Mapper\Base;
use Magento\Framework\DataObject;

/**
 * Class Field
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor
 */
class Field extends DataObject implements MergerInterface
{
    /**
     * @var Base
     */
    private $mapper;

    /**
     * @param Base $mapper
     * @param array $data
     */
    public function __construct(
        Base $mapper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function merge($rows, $compareRows, $dataSourceData)
    {
        $numberColumns = $dataSourceData['number_columns'];
        $rows = $this->mergeArray($rows, $compareRows, $numberColumns);

        return $rows;
    }

    /**
     * Merge data from two arrays
     *
     * @param array $rows
     * @param array $compareRows
     * @param array $numberColumns
     * @return array
     * @throws \Exception
     */
    protected function mergeArray($rows, $compareRows, $numberColumns)
    {
        foreach ($compareRows as $compareRowIndex => $compareRowValues) {
            $rowIndex = $this->getIndexByField($rows, $compareRowValues);

            $rows[$rowIndex] = isset($rows[$rowIndex])
                ? $this->mergeRowValues($rows[$rowIndex], $compareRowValues)
                : $this->mapper->mapRow($compareRowValues, $numberColumns, true);
            $rows[$rowIndex]['merged_row'] = true;
        }

        foreach ($rows as $index => $values) {
            if (!isset($values['merged_row'])) {
                $rows[$index] = $this->mapper->mapRow($values, $numberColumns, false);
            }
        }

        return $rows;
    }

    /**
     * Merge row values
     *
     * @param array $rows
     * @param array $compareRowValues
     * @return array
     */
    protected function mergeRowValues($rows, $compareRowValues)
    {
        foreach ($compareRowValues as $index => $value) {
            $rows['c_' . $index] = $value;
        }

        return $rows;
    }

    /**
     * Retrieve row index by field
     *
     * @param array $rows
     * @param array $compareRowValue
     * @return int
     */
    protected function getIndexByField($rows, $compareRowValue)
    {
        foreach ($rows as $rowIndex => $rowValue) {
            if ($this->isEqualsValues($rowValue, $compareRowValue)) {
                return $rowIndex;
            }
        }

        return count($rows);
    }

    /**
     * Check if equals values
     *
     * @param array $rowValue
     * @param array $compareRowValue
     * @return bool
     */
    protected function isEqualsValues($rowValue, $compareRowValue)
    {
        $field = $this->getData('merge_by_field');
        if (!is_array($field)) {
            return $rowValue[$field] == $compareRowValue[$field];
        }

        foreach ($field as $fieldName) {
            if ($rowValue[$fieldName] != $compareRowValue[$fieldName]) {
                return false;
            }
        }

        return true;
    }
}
