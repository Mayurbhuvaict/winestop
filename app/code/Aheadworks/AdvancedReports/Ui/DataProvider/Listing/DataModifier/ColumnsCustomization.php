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



































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifier;

use Aheadworks\AdvancedReports\Ui\Component\Inspector as ComponentInspector;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifierInterface;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\ConfigInterface;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomizationInterface as CustomColumnInterface;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\Config\ColumnCustomization\Provider;

/**
 * Class ColumnsCustomization
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifier
 */
class ColumnsCustomization extends AbstractDataModifier implements DataModifierInterface
{
    /**
     * @var Provider
     */
    private $columnCustomizationProvider;

    /**
     * @param ComponentInspector $componentInspector
     * @param Provider $columnCustomizationProvider
     */
    public function __construct(
        ComponentInspector $componentInspector,
        Provider $columnCustomizationProvider
    ) {
        parent::__construct($componentInspector);
        $this->columnCustomizationProvider = $columnCustomizationProvider;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareSourceData($data, UiComponentInterface $component)
    {
        return array_merge(
            $data,
            [
                'report_configuration' => [
                    ConfigInterface::COLUMNS_CUSTOMIZATION => $this->getColumnsCustomizationData($component)
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareComponentData(UiComponentInterface $component)
    {
        $this->applyCustomColumnLabels($component);
    }

    /**
     * Returns columns customization data
     *
     * @param UiComponentInterface $component
     * @return array
     * @throws LocalizedException
     */
    private function getColumnsCustomizationData(UiComponentInterface $component)
    {
        $columns = $this->componentInspector->getColumns($component);
        $customizedColumns = $this->columnCustomizationProvider->getColumnListForComponent($component);
        $customizationColumnData = [];
        $recordId = 0;
        foreach ($columns as $column) {
            $customizedColumn = $customizedColumns[$column->getName()] ?? null;
            $customizationColumnData[] = [
                CustomColumnInterface::COLUMN_NAME => $column->getName(),
                'original_label'=> $column->getData('config/original_label') ?? $column->getData('config/label'),
                CustomColumnInterface::CUSTOM_LABEL => $customizedColumn ? $customizedColumn->getCustomLabel() : '',
                CustomColumnInterface::IS_EXPORTED_TO_EMAIL =>
                    $customizedColumn ? $customizedColumn->isExportedToEmail() : '1',
                'record_id' => $recordId
            ];
            $recordId ++;
        }

        return $customizationColumnData;
    }

    /**
     * Apply custom column labels
     *
     * @param UiComponentInterface $component
     * @throws LocalizedException
     */
    private function applyCustomColumnLabels(UiComponentInterface $component)
    {
        $customizedColumns = $this->columnCustomizationProvider->getColumnListForComponent($component);
        $columns = $this->componentInspector->getColumns($component);
        foreach ($columns as $column) {
            $customizedColumn = $customizedColumns[$column->getName()] ?? null;
            if ($customizedColumn && $customizedColumn->getCustomLabel()) {
                $config = $column->getData('config');
                $config['original_label'] = $config['label'];
                $config['label'] = $customizedColumn->getCustomLabel();
                $column->setData('config', $config);
            }
        }
    }
}
