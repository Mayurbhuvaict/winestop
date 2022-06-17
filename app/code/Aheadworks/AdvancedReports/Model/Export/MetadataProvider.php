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












































































































































































namespace Aheadworks\AdvancedReports\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentInterface;
use Aheadworks\AdvancedReports\Ui\Component\Inspector as ComponentInspector;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\FormatterPool;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomization\Provider
    as ColumnCustomizationProvider;

/**
 * Class MetadataProvider
 *
 * @package Aheadworks\AdvancedReports\Model\Export
 */
class MetadataProvider
{
    /**
     * @var ComponentInspector
     */
    private $componentInspector;

    /**
     * @var FormatterPool
     */
    private $formatterPool;

    /**
     * @var ColumnCustomizationProvider
     */
    private $columnCustomizationProvider;

    /**
     * @param ComponentInspector $componentInspector
     * @param FormatterPool $formatterPool
     * @param ColumnCustomizationProvider $columnCustomizationProvider
     */
    public function __construct(
        ComponentInspector $componentInspector,
        FormatterPool $formatterPool,
        ColumnCustomizationProvider $columnCustomizationProvider
    ) {
        $this->componentInspector = $componentInspector;
        $this->formatterPool = $formatterPool;
        $this->columnCustomizationProvider = $columnCustomizationProvider;
    }

    /**
     * Prepare row with headers for export
     *
     * @param UiComponentInterface $component
     * @return string[]
     * @throws LocalizedException
     */
    public function getHeaders(UiComponentInterface $component)
    {
        $row = [];
        $customizedColumns = $this->columnCustomizationProvider->getColumnListForComponent($component);
        foreach ($this->componentInspector->getColumns($component) as $column) {
            if ($this->isColumnAllowed($column->getName(), $customizedColumns)) {
                $row[] = $column->getData('config/label');
            }
        }

        return $row;
    }

    /**
     * Prepare row with fields used for export
     *
     * @param UiComponentInterface $component
     * @return array
     * @throws LocalizedException
     */
    public function getFields(UiComponentInterface $component)
    {
        $row = [];
        $customizedColumns = $this->columnCustomizationProvider->getColumnListForComponent($component);
        foreach ($this->componentInspector->getColumns($component) as $column) {
            if ($this->isColumnAllowed($column->getName(), $customizedColumns)) {
                $row[] = $column->getName();
            }
        }

        return $row;
    }

    /**
     * Prepare row data
     *
     * @param UiComponentInterface $component
     * @param DocumentInterface $document
     * @param array $fields
     * @return array
     * @throws LocalizedException
     */
    public function getRowData($component, $document, $fields)
    {
        $row = [];
        foreach ($fields as $column) {
            $value = $document->getCustomAttribute($column)->getValue();
            $dataExportType = $this->componentInspector->getColumnDataExportType($component, $column);
            $formatter = $this->formatterPool->getFormatter($dataExportType);
            $row[] = $formatter->format($column, $value);
        }

        return $row;
    }

    /**
     * Returns total row data
     *
     * @param UiComponentInterface $component
     * @param array $totalsItem
     * @param array $fields
     * @return array
     * @throws LocalizedException
     */
    public function getTotalRowData($component, $totalsItem, $fields)
    {
        $row = [];
        foreach ($fields as $index => $column) {
            $result = '';
            if (isset($totalsItem[$column]) && $index != 0) {
                $isVisibleInTotals = $this->componentInspector->isColumnVisibleInTotals($component, $column);
                if ($isVisibleInTotals) {
                    $dataExportType = $this->componentInspector->getColumnDataExportType($component, $column);
                    $formatter = $this->formatterPool->getFormatter($dataExportType);
                    $result = $formatter->format($column, $totalsItem[$column]);
                }
            }

            $row[] = $result;
        }
        return $row;
    }

    /**
     * Is column is allowed in email
     *
     * @param string $columnName
     * @param ColumnCustomizationInterface[] $customizedColumnList
     * @return bool
     */
    private function isColumnAllowed($columnName, $customizedColumnList)
    {
        $customizedColumn = $customizedColumnList[$columnName] ?? null;
        if ($customizedColumn) {
            return (bool)$customizedColumn->isExportedToEmail();
        }

        return true;
    }
}
