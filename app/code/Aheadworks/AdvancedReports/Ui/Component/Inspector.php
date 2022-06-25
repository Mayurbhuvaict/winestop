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






































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Component\Form\Element\Select as SelectElement;

/**
 * Class Inspector
 *
 * @package Aheadworks\AdvancedReports\Ui\Component
 */
class Inspector
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $options;

    /**
     * Returns columns list
     *
     * @param UiComponentInterface $component
     * @return UiComponentInterface[]
     * @throws LocalizedException
     */
    public function getColumns(UiComponentInterface $component)
    {
        if (!isset($this->columns[$component->getName()])) {
            $columns = $this->getColumnsComponent($component);
            foreach ($columns->getChildComponents() as $column) {
                if ($column->getData('config/label') && $column->getData('config/dataType') !== 'actions') {
                    $this->columns[$component->getName()][$column->getName()] = $column;
                }
            }
        }
        return $this->columns[$component->getName()];
    }

    /**
     * Get column data export type
     *
     * @param UiComponentInterface $component
     * @param string $column
     * @return string
     * @throws LocalizedException
     */
    public function getColumnDataExportType(UiComponentInterface $component, $column)
    {
        $columns = $this->getColumns($component);
        return isset($columns[$column]) && $columns[$column]->getData('config/dataExportType')
            ? $columns[$column]->getData('config/dataExportType')
            : 'text';
    }

    /**
     * Is column visible in totals
     *
     * @param UiComponentInterface $component
     * @param string $column
     * @return string
     * @throws LocalizedException
     */
    public function isColumnVisibleInTotals(UiComponentInterface $component, $column)
    {
        $columns = $this->getColumns($component);
        return isset($columns[$column])
            ? (bool)$columns[$column]->getData('config/totalsVisible')
            : true;
    }

    /**
     * Get column data export type
     *
     * @param UiComponentInterface $component
     * @param string $column
     * @return string
     * @throws LocalizedException
     */
    public function getColumnObject(UiComponentInterface $component, $column)
    {
        $columns = $this->getColumns($component);
        return isset($columns[$column])
            ? $columns[$column]
            : null;
    }

    /**
     * Get options for column
     *
     * @param UiComponentInterface $component
     * @param string $columnName
     * @return array
     * @throws LocalizedException
     */
    public function getOptions(UiComponentInterface $component, $columnName)
    {
        if (!isset($this->options[$component->getName()][$columnName])) {
            $columns = $this->getColumns($component);
            foreach ($columns as $column) {
                if ($column->getData('config/dataType') == SelectElement::NAME) {
                    $this->options[$component->getName()][$column->getName()] =
                        $this->prepareOptions($column);
                }
            }
        }
        return $this->options[$component->getName()][$columnName];
    }

    /**
     * Prepare array of Select options
     *
     * @param string $column
     * @return array
     */
    private function prepareOptions($column)
    {
        $options = [];
        foreach ($column->getData('config/options') as $option) {
            if (!is_array($option['value'])) {
                $options[$option['value']] = $option['label'];
            } else {
                $this->getComplexLabel(
                    $option['value'],
                    $option['label'],
                    $options
                );
            }
        }
        return $options;
    }

    /**
     * Get complex option label
     *
     * @param array $list
     * @param string $label
     * @param array $output
     * @return void
     */
    private function getComplexLabel($list, $label, &$output)
    {
        foreach ($list as $item) {
            if (!is_array($item['value'])) {
                $output[$item['value']] = $label . $item['label'];
            } else {
                $this->getComplexLabel($item['value'], $label . $item['label'], $output);
            }
        }
    }

    /**
     * Returns Columns component
     *
     * @param UiComponentInterface $component
     * @return UiComponentInterface
     * @throws LocalizedException
     */
    private function getColumnsComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $childComponent) {
            if ($childComponent instanceof Columns) {
                return $childComponent;
            }
        }
        throw new LocalizedException(__('No columns component found'));
    }
}
