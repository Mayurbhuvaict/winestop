<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow;

/**
 * Class Mapper
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow
 */
class Mapper
{
    /**
     * @var array
     */
    private $map = [
        'prefix' => 'name-field-row',
        'firstname' => 'name-field-row',
        'middlename' => 'name-field-row',
        'lastname' => 'name-field-row',
        'suffix' => 'name-field-row',
        'company' => 'phone-company-field-row',
        'street' => 'address-field-row',
        'city' => 'city-field-row',
        'country_id' => 'included-country-field-row',
        'region' => 'included-country-field-row',
        'region_id' => 'included-country-field-row',
        'postcode' => 'included-country-field-row',
        'telephone' => 'phone-company-field-row',
        'fax' => 'phone-company-field-row',
        'vat_id' => 'vat-field-row'
    ];

    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @param Configurator $configurator
     */
    public function __construct(
        Configurator $configurator
    ) {
        $this->configurator = $configurator;
    }

    /**
     * Map to attributes
     *
     * @param string $fieldRow
     * @return array
     */
    public function toAttributes($fieldRow)
    {
        $attributes = [];
        foreach ($this->map as $attributeCode => $row) {
            if ($row == $fieldRow) {
                $attributes[] = $attributeCode;
            }
        }
        return $attributes;
    }

    /**
     * Map to default field row
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function toDefaultFieldRow($attributeCode)
    {
        return isset($this->map[$attributeCode])
            ? $this->map[$attributeCode]
            : null;
    }

    /**
     * Map to custom field row
     *
     * @param string $attributeCode
     * @param string $addressType
     * @return string|null
     */
    public function toCustomFieldRow($attributeCode, $addressType)
    {
        $customizationConfig = $this->configurator->getCustomizationConfig($addressType);
        return isset($customizationConfig['fields'][$attributeCode]['row_name'])
            ? $customizationConfig['fields'][$attributeCode]['row_name']
            : null;
    }
}
