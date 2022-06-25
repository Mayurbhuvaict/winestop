<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow;

use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;

/**
 * Class Configurator
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow
 */
class Configurator
{
    /**
     * Address row name with country field included
     */
    const INCLUDED_COUNTRY_FIELD_ROW_NAME = 'included-country-field-row';

    /**
     * @var array
     */
    private $relatedAttributes = [
        'region' => ['region_id']
    ];

    /**
     * @var array exclusive name list for rows with specific field included
     */
    private $exclusiveRowNames = [
        'country_id' => self::INCLUDED_COUNTRY_FIELD_ROW_NAME
    ];

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var DefaultFieldMoving
     */
    private $defaultFieldMoving;

    /**
     * @param ModuleConfig $config
     * @param DefaultFieldMoving $defaultFieldMoving
     */
    public function __construct(
        ModuleConfig $config,
        DefaultFieldMoving $defaultFieldMoving
    ) {
        $this->config = $config;
        $this->defaultFieldMoving = $defaultFieldMoving;
    }

    /**
     * Get customization configuration
     *
     * @param string $addressType
     * @return array
     */
    public function getCustomizationConfig($addressType)
    {
        if (!isset($this->configuration[$addressType])) {
            $this->configuration[$addressType] = [];
            $formConfig = $this->config->getAddressFormConfig($addressType);
            if (isset($formConfig['attributes']) && is_array($formConfig['attributes'])) {
                $rowCounter = 0;
                $extraLinesInserted = 0;
                foreach ($formConfig['attributes'] as $attributeName => $attributeConfig) {
                    $sortOrder = $formConfig['rows'][$attributeName]['sort_order'] ?? 0;
                    if (count($attributeConfig) != count($attributeConfig, COUNT_RECURSIVE)) {
                        foreach ($attributeConfig as $line => $lineAttributeConfig) {
                            if ($this->isNewRowRequired($attributeName, $lineAttributeConfig, $line)) {
                                $rowCounter ++;
                            }

                            $this->prepareAttributeField(
                                $addressType,
                                $rowCounter,
                                $sortOrder + $extraLinesInserted,
                                $attributeName . $line
                            );
                            $extraLinesInserted++;
                        }
                    } else {
                        if ($this->isNewRowRequired($attributeName, $attributeConfig)) {
                            $rowCounter ++;
                        }
                        $this->prepareAttributeField(
                            $addressType,
                            $rowCounter,
                            $sortOrder + $extraLinesInserted,
                            $attributeName
                        );
                    }
                }

                $this->replaceRowNamesWithExclusive($addressType);
            }
        }

        return $this->configuration[$addressType];
    }

    /**
     * Prepare attribute field
     *
     * @param string $addressType
     * @param int $rowCounter
     * @param string $sortOrder
     * @param string $attributeName
     */
    private function prepareAttributeField($addressType, $rowCounter, $sortOrder, $attributeName)
    {
        $currentRowName = 'field-row-' . $rowCounter;
        $this->configuration[$addressType]['fields'][$attributeName] = [
            'row_name' => $currentRowName,
            'sort_order' => $sortOrder
        ];
        $this->configuration[$addressType]['sort_orders'][$currentRowName] = $rowCounter;

        if (isset($this->relatedAttributes[$attributeName])) {
            foreach ($this->relatedAttributes[$attributeName] as $relatedAttribute) {
                $this->configuration[$addressType]['fields'][$relatedAttribute] =
                    $this->configuration[$addressType]['fields'][$attributeName];
            }
        }
    }

    /**
     * Adjust row names with specific fields
     *
     * @param string $addressType
     */
    private function replaceRowNamesWithExclusive($addressType)
    {
        foreach ($this->exclusiveRowNames as $fieldToFind => $exclusiveRowName) {
            if (array_key_exists($fieldToFind, $this->configuration[$addressType]['fields'])) {
                $oldRowName = $this->configuration[$addressType]['fields'][$fieldToFind]['row_name'];
                $oldSortOrder = $this->configuration[$addressType]['sort_orders'][$oldRowName];
                foreach ($this->configuration[$addressType]['fields'] as $fieldName => $fieldConfig) {
                    if ($oldRowName == $fieldConfig['row_name']) {
                        $this->configuration[$addressType]['fields'][$fieldName]['row_name'] = $exclusiveRowName;
                        unset($this->configuration[$addressType]['sort_orders'][$oldRowName]);
                        $this->configuration[$addressType]['sort_orders'][$exclusiveRowName] = $oldSortOrder;
                    }
                }
            }
        }
    }

    /**
     * Check if new row required
     *
     * @param string $attributeName
     * @param array $attributeConfig
     * @param int|null $lineNumber
     * @return bool
     */
    private function isNewRowRequired($attributeName, $attributeConfig, $lineNumber = null)
    {
        if (!isset($attributeConfig['is_moved'])) {
            return  $this->defaultFieldMoving->get($attributeName, $lineNumber) ?
                !$this->defaultFieldMoving->get($attributeName, $lineNumber) : true;
        }

        return !(bool)$attributeConfig['is_moved'] && (bool)$attributeConfig['visible'];
    }
}
