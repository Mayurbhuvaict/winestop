<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes;

use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\Mapper as FieldRowMapper;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Merger
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes
 */
class Merger
{
    /**
     * @var array
     */
    private $formElementMap = [
        'checkbox' => 'Magento_Ui/js/form/element/select',
        'select' => 'Magento_Ui/js/form/element/select',
        'textarea'  => 'Magento_Ui/js/form/element/textarea',
        'multiline' => 'Magento_Ui/js/form/components/group',
        'multiselect' => 'Magento_Ui/js/form/element/multiselect',
    ];

    /**
     * @var array
     */
    private $templateMap = [
        'image' => 'ui/form/element/media',
        'input' => 'Aheadworks_OneStepCheckout/form/element/input'
    ];

    /**
     * @var array
     */
    private $inputValidationMap = [
        'alpha' => 'validate-alpha',
        'numeric' => 'validate-number',
        'alphanumeric' => 'validate-alphanum',
        'url' => 'validate-url',
        'email' => 'email2',
    ];

    /**
     * @var array
     */
    private $initiallyHiddenFields = ['region'];

    /**
     * @var FieldRowMapper
     */
    private $fieldRowMapper;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var bool
     */
    private $shouldRemoveOptions;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param FieldRowMapper $fieldRowMapper
     * @param ProductMetadataInterface $productMetadata
     * @param Config $config
     */
    public function __construct(
        FieldRowMapper $fieldRowMapper,
        ProductMetadataInterface $productMetadata,
        Config $config
    ) {
        $this->fieldRowMapper = $fieldRowMapper;
        $this->productMetadata = $productMetadata;
        $this->config = $config;
    }

    /**
     * Merge additional address fields for given provider
     *
     * @param array $elements
     * @param string $providerName
     * @param string $addressType
     * @param array $fieldRows
     * @return array
     */
    public function merge($elements, $providerName, $addressType, array $fieldRows = [])
    {
        $rows = [];
        foreach ($elements as $attributeCode => $attributeConfig) {
            if ($attributeConfig['formElement'] == 'multiline') {
                for ($lineIndex = 0; $lineIndex < (int)$attributeConfig['size']; $lineIndex ++) {
                    $rows = $this->prepareRowConfig(
                        $attributeCode,
                        $attributeConfig,
                        $providerName,
                        $addressType,
                        $rows,
                        $fieldRows,
                        $lineIndex
                    );
                }
            } else {
                $rows = $this->prepareRowConfig(
                    $attributeCode,
                    $attributeConfig,
                    $providerName,
                    $addressType,
                    $rows,
                    $fieldRows
                );
            }
        }

        return $rows;
    }

    /**
     * Prepare row config
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param string $providerName
     * @param string $addressType
     * @param array $rows
     * @param array $fieldRows
     * @param null|int $line
     * @return array
     */
    public function prepareRowConfig(
        $attributeCode,
        $attributeConfig,
        $providerName,
        $addressType,
        array $rows = [],
        array $fieldRows = [],
        $line = null
    ) {
        $dataScopePrefix = $addressType . 'Address';
        $customAttributeRow = $attributeCode;
        if ($line !== null) {
            $customAttributeRow = $attributeCode . $line;
        }

        $defaultFieldRow = $this->fieldRowMapper->toDefaultFieldRow($attributeCode);
        $customFieldRow = $this->fieldRowMapper->toCustomFieldRow(
            $customAttributeRow,
            $addressType
        );
        $row = $customFieldRow ?? $defaultFieldRow ?? $attributeCode . '-field-row';

        if (!array_key_exists($row, $rows)) {
            $rows[$row] = !$customFieldRow && $defaultFieldRow
                ? $fieldRows[$defaultFieldRow]
                : $this->getNewRowConfig();
        }

        $additionalConfig = $fieldRows[$defaultFieldRow]['children'][$attributeCode] ?? [];
        if ($this->isFieldVisible($attributeConfig, $additionalConfig)
            || $this->isFieldInitiallyHidden($attributeCode)
        ) {
            if ($line !== null) {
                $childFieldConfig = $this->getMultilineFieldConfig(
                    $line,
                    $attributeCode,
                    $attributeConfig,
                    $additionalConfig,
                    $providerName,
                    $dataScopePrefix,
                    $addressType
                );
            } else {
                $childFieldConfig = $this->getFieldConfig(
                    $attributeCode,
                    $attributeConfig,
                    $additionalConfig,
                    $providerName,
                    $dataScopePrefix
                );
            }

            $rows[$row]['children'][$customAttributeRow] = $childFieldConfig;
        }

        $this->resolveRowTemplate($rows[$row]);

        return $rows;
    }

    /**
     * Get new field row config
     *
     * @return array
     */
    private function getNewRowConfig()
    {
        return [
            'component' => 'uiComponent',
            'config' => [
                'template' => 'Aheadworks_OneStepCheckout/form/field-row'
            ],
            'children' => []
        ];
    }

    /**
     * Apply row template depending on element count
     *
     * @param array $row
     */
    private function resolveRowTemplate(&$row)
    {
        $row['config']['template'] = count($row) > 1
            ? 'Aheadworks_OneStepCheckout/form/field-row-fluid'
            : 'Aheadworks_OneStepCheckout/form/field-row';
    }

    /**
     * Retrieve UI field configuration for given attribute
     *
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param array $additionalConfig
     * @param string $providerName
     * @param string $dataScopePrefix
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getFieldConfig(
        $attributeCode,
        array $attributeConfig,
        array $additionalConfig,
        $providerName,
        $dataScopePrefix
    ) {
        if (isset($attributeConfig['validation']['input_validation'])) {
            $validationRule = $attributeConfig['validation']['input_validation'];
            $attributeConfig['validation'][$this->inputValidationMap[$validationRule]] = true;
            unset($attributeConfig['validation']['input_validation']);
        }

        $uiComponent = isset($this->formElementMap[$attributeConfig['formElement']])
            ? $this->formElementMap[$attributeConfig['formElement']]
            : 'Magento_Ui/js/form/element/abstract';
        $elementTemplate = isset($this->templateMap[$attributeConfig['formElement']])
            ? $this->templateMap[$attributeConfig['formElement']]
            : 'ui/form/element/' . $attributeConfig['formElement'];
        $dataScope = isset($attributeConfig['system']) && $attributeConfig['system'] == true
            ? $dataScopePrefix . '.' . $attributeCode
            : $dataScopePrefix . '.custom_attributes.' . $attributeCode;

        $element = [
            'component' => isset($additionalConfig['component']) ? $additionalConfig['component'] : $uiComponent,
            'config' => [
                'customScope' => $dataScopePrefix,
                'customEntry' => isset($additionalConfig['config']['customEntry'])
                    ? $additionalConfig['config']['customEntry']
                    : null,
                'template' => 'Aheadworks_OneStepCheckout/form/field',
                'elementTmpl' => isset($additionalConfig['config']['elementTmpl'])
                    ? $additionalConfig['config']['elementTmpl']
                    : $elementTemplate,
                'tooltip' => isset($additionalConfig['config']['tooltip'])
                    ? $additionalConfig['config']['tooltip']
                    : null
            ],
            'dataScope' => $dataScope,
            'label' => $attributeConfig['label'],
            'provider' => $providerName,
            'sortOrder' => isset($additionalConfig['sortOrder'])
                ? $additionalConfig['sortOrder']
                : $attributeConfig['sortOrder'],
            'validation' => $this->mergeConfigurationNode('validation', $additionalConfig, $attributeConfig),
            'options' => isset($attributeConfig['options']) ? $attributeConfig['options'] : [],
            'filterBy' => isset($additionalConfig['filterBy']) ? $additionalConfig['filterBy'] : null,
            'customEntry' => isset($additionalConfig['customEntry']) ? $additionalConfig['customEntry'] : null,
            'countryFieldIncludedRow' => $additionalConfig['countryFieldIncludedRow'] ?? null,
            'imports' => $additionalConfig['imports'] ?? null,
        ];

        if (($attributeCode === 'region_id' || $attributeCode === 'country_id') && $this->shouldRemoveOptions()) {
            unset($element['options']);
            $element['deps'] = [$providerName];
            $element['imports'] = [
                'initialOptions' => 'index = ' . $providerName . ':dictionaries.' . $attributeCode,
                'setOptions' => 'index = ' . $providerName . ':dictionaries.' . $attributeCode
            ];
        }
        if (isset($additionalConfig['visible'])) {
            $element['visible'] = $additionalConfig['visible'];
        } elseif ($this->isFieldInitiallyHidden($attributeCode)) {
            $element['visible'] = false;
        } else {
            $element['visible'] = true;
        }
        if (isset($attributeConfig['value']) && $attributeConfig['value'] != null) {
            $element['value'] = $attributeConfig['value'];
        } elseif (isset($attributeConfig['default']) && $attributeConfig['default'] != null) {
            $element['value'] = $attributeConfig['default'];
        }
        if (isset($additionalConfig['config']['additionalClasses'])) {
            $element['config']['additionalClasses'] = $additionalConfig['config']['additionalClasses'];
        }

        return $element;
    }

    /**
     * Merge two configuration nodes recursively
     *
     * @param string $nodeName
     * @param array $mainSource
     * @param array $additionalSource
     * @return array
     */
    private function mergeConfigurationNode($nodeName, array $mainSource, array $additionalSource)
    {
        $mainData = isset($mainSource[$nodeName]) ? $mainSource[$nodeName] : [];
        $additionalData = isset($additionalSource[$nodeName]) ? $additionalSource[$nodeName] : [];
        return array_replace_recursive($additionalData, $mainData);
    }

    /**
     * Check if address attribute is visible on frontend
     *
     * @param array $attributeConfig
     * @param array $additionalConfig
     * @return bool
     */
    private function isFieldVisible(array $attributeConfig, array $additionalConfig = [])
    {
        if ($attributeConfig['visible'] == false
            || (isset($additionalConfig['visible']) && $additionalConfig['visible'] == false)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Check if field initially hidden
     *
     * @param string $attributeCode
     * @return bool
     */
    private function isFieldInitiallyHidden($attributeCode)
    {
        return in_array($attributeCode, $this->initiallyHiddenFields);
    }

    /**
     * Retrieve field configuration for street address attribute
     *
     * @param int $lineIndex
     * @param string $attributeCode
     * @param array $attributeConfig
     * @param array $additionalConfig
     * @param string $providerName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @param string $addressType
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getMultilineFieldConfig(
        $lineIndex,
        $attributeCode,
        array $attributeConfig,
        array $additionalConfig,
        $providerName,
        $dataScopePrefix,
        $addressType
    ) {
        $lines = [];
        $formConfig = $this->config->getAddressFormConfig($addressType);
        if ($attributeCode == 'street') {
            $attributes = isset($formConfig['attributes'])
                ? $formConfig['attributes']['street'] ?? []
                : [];
            if (isset($attributes[$lineIndex]) && !$attributes[$lineIndex]['required']) {
                unset($attributeConfig['validation']['required-entry']);
            }
        }

        $isFirstLine = $lineIndex === 0;
        $label = isset($additionalConfig['label'])
            ? $additionalConfig['label']
            : $attributeConfig['label'];
        $line = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'label' => __($label . ' Line' . ($isFirstLine ? '' : ' ' . ($lineIndex + 1))),
            'config' => [
                'customScope' => $dataScopePrefix,
                'template' => 'Aheadworks_OneStepCheckout/form/field-multiline',
                'elementTmpl' => $isFirstLine && $attributeCode == 'street'
                    ? 'Aheadworks_OneStepCheckout/form/element/input-autocomplete'
                    : $this->templateMap['input']
            ],
            'dataScope' => $lineIndex,
            'provider' => $providerName,
            'validation' => $isFirstLine
                ? array_merge(
                    ['required-entry' => (bool)$attributeConfig['required']],
                    $attributeConfig['validation']
                )
                : $attributeConfig['validation']

        ];
        if ($isFirstLine && isset($attributeConfig['default']) && $attributeConfig['default'] != null) {
            $line['value'] = $attributeConfig['default'];
        }
        $lines[$lineIndex] = $line;

        return [
            'component' => 'Magento_Ui/js/form/components/group',
            'required' => (bool)$attributeConfig['required'],
            'dataScope' => $dataScopePrefix . '.' . $attributeCode,
            'provider' => $providerName,
            'sortOrder' => $attributeConfig['sortOrder'],
            'type' => 'group',
            'label' => '',
            'config' => [
                'template' => 'ui/group/group',
                'fieldTemplate' => 'Aheadworks_OneStepCheckout/form/field-multiline',
                'additionalClasses' => $attributeCode
            ],
            'children' => $lines,
        ];
    }

    /**
     * Should remove country and region options
     *
     * @return bool
     */
    private function shouldRemoveOptions()
    {
        if ($this->shouldRemoveOptions === null) {
            $this->shouldRemoveOptions = version_compare($this->productMetadata->getVersion(), '2.1.8', '>=');
        }
        return $this->shouldRemoveOptions;
    }
}
