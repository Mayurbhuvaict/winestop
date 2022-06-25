<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\Address;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\Mapper as FieldRowMapper;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultFieldMoving;
use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\OneStepCheckout\Model\Address\Attribute\Code\Resolver as AddressAttributeCodeResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Customization
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\Address
 */
class Customization extends Field
{
    /**
     * @var string
     */
    private $addressType;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/address/customization.phtml';

    /**
     * @var array
     */
    private $booleanMetaFields = [
        'visible' => 'Enable',
        'required' => 'Required',
        'is_moved' => 'Move next to previous field'
    ];

    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var FieldRowMapper
     */
    private $fieldRowMapper;

    /**
     * @var Config
     */
    private $customizationConfig;

    /**
     * @var DefaultSortOrder
     */
    private $defaultSortOrder;

    /**
     * @var AddressAttributeCodeResolver
     */
    private $addressAttributeCodeResolver;

    /**
     * @var DefaultFieldMoving
     */
    private $defaultFieldMoving;

    /**
     * @param Context $context
     * @param AddressMetadataInterface $addressMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param FieldRowMapper $fieldRowMapper
     * @param Config $customizationConfig
     * @param DefaultSortOrder $defaultSortOrder
     * @param AddressAttributeCodeResolver $addressAttributeCodeResolver
     * @param DefaultFieldMoving $defaultFieldMoving
     * @param string $addressType
     * @param array $data
     */
    public function __construct(
        Context $context,
        AddressMetadataInterface $addressMetadata,
        AvailabilityChecker $availabilityChecker,
        FieldRowMapper $fieldRowMapper,
        Config $customizationConfig,
        DefaultSortOrder $defaultSortOrder,
        AddressAttributeCodeResolver $addressAttributeCodeResolver,
        DefaultFieldMoving $defaultFieldMoving,
        $addressType = 'default',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->addressMetadata = $addressMetadata;
        $this->availabilityChecker = $availabilityChecker;
        $this->fieldRowMapper = $fieldRowMapper;
        $this->customizationConfig = $customizationConfig;
        $this->defaultSortOrder = $defaultSortOrder;
        $this->addressAttributeCodeResolver = $addressAttributeCodeResolver;
        $this->defaultFieldMoving = $defaultFieldMoving;
        $this->addressType = $addressType;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Get attribute codes grouped by field row ids
     *
     * @return array
     */
    public function getAttrCodesGroupedByFieldRow()
    {
        $data = [];
        $prevSortOrder = null;
        $attributes = $this->addressMetadata->getAttributes('customer_register_address');
        foreach ($attributes as $attributesMeta) {
            if (!$this->availabilityChecker->isAvailableOnForm($attributesMeta)) {
                continue;
            }
            $attributeCode = $attributesMeta->getAttributeCode();
            $duplicatedAttrCode = $this->addressAttributeCodeResolver->getDuplicatedAttributeCode($attributeCode);
            if ($duplicatedAttrCode && array_key_exists($duplicatedAttrCode, $data)) {
                continue;
            }

            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = [];
            }
            $data[$attributeCode][] = $attributeCode;
        }
        uksort($data, [$this, 'compareFieldRows']);
        return $data;
    }

    /**
     * Get field row sort order
     *
     * @param string $rowId
     * @return int|bool
     */
    public function getFieldRowSortOrder($rowId)
    {
        $value = $this->getElement()->getValue();
        if (isset($value['rows'][$rowId])) {
            return $value['rows'][$rowId]['sort_order'];
        } else {
            return $this->defaultSortOrder->getRowSortOrder($rowId);
        }
    }

    /**
     * Compare field row Ids by sort order
     *
     * @param string $rowId1
     * @param string $rowId2
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function compareFieldRows($rowId1, $rowId2)
    {
        $row1SortOrder = $this->getFieldRowSortOrder($rowId1);
        $row2SortOrder = $this->getFieldRowSortOrder($rowId2);

        if ($row1SortOrder > $row2SortOrder) {
            return 1;
        } elseif ($row1SortOrder < $row2SortOrder) {
            return -1;
        }

        return 0;
    }

    /**
     * Get attribute form values
     *
     * @param string $attributeCode
     * @param array $defaultValues
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeFormValues($attributeCode, $defaultValues)
    {
        $value = $this->getElement()->getValue();
        if (isset($value['attributes'][$attributeCode])) {
            $formValues = $value['attributes'][$attributeCode];
            if ($this->isMultiline($attributeCode)) {
                $lineCount = $this->getMultilineCount($attributeCode);
                for ($line = 0; $line < $lineCount; $line ++) {
                    if (!isset($formValues[$line])) {
                        $formValues[$line] = $defaultValues[$line];
                    }

                    $formValues[$line]['is_moved'] = $formValues[$line]['is_moved']
                        ?? $this->defaultFieldMoving->get($attributeCode, $line);
                }

                if (!array_key_exists(0, $formValues)) {
                    $formValues = [$formValues];
                }
                if (count($formValues) != $lineCount) {
                    $defaultValues = $this->getAttributeFormDefaultValues($attributeCode);
                    $formValues = array_replace($defaultValues, $formValues);
                }
            } else {
                if (array_key_exists(0, $formValues)) {
                    $formValues = $formValues[0];
                }
            }
            $formValues['is_moved'] = $formValues['is_moved'] ?? $this->defaultFieldMoving->get($attributeCode);
            return $formValues;
        } else {
            return $this->getAttributeFormDefaultValues($attributeCode);
        }
    }

    /**
     * Get attribute form default values
     *
     * @param string $attributeCode
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeFormDefaultValues($attributeCode)
    {
        $defaultValues = [];
        $metadata = $this->addressMetadata->getAttributeMetadata($attributeCode);
        $label = $metadata->getFrontendLabel();
        if ($this->isMultiline($attributeCode)) {
            $lineCount = $this->getMultilineCount($attributeCode);
            for ($line = 0; $line < $lineCount; $line ++) {
                $isFirstLine = ($line == 0);
                $defaultValues[$line] = [
                    'visible' => true,
                    'is_moved' => $this->defaultFieldMoving->get($attributeCode, $line),
                    'required' => $isFirstLine,
                    'label' => $isFirstLine ? $label . ' Line' : $label . ' Line ' . ($line + 1)
                ];
            }
        } else {
            $isVisible = $metadata->isVisible();
            if ($attributeCode == 'vat_id') {
                $isVisible = $this->_scopeConfig->isSetFlag(
                    AddressHelper::XML_PATH_VAT_FRONTEND_VISIBILITY,
                    ScopeInterface::SCOPE_STORE
                );
            }
            $defaultValues = [
                'is_moved' => $this->defaultFieldMoving->get($attributeCode),
                'visible' => $isVisible,
                'required' => $metadata->isRequired(),
                'label' => $label
            ];
        }
        return $defaultValues;
    }

    /**
     * Check if modification of metadata field is allowed
     *
     * @param string $attributeCode
     * @param string $name
     * @param int|null $line
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function canModifyMeta($attributeCode, $name, $line = null)
    {
        $metaEditRestrictions = $this->customizationConfig->get($this->addressType);
        if ($this->isMultiline($attributeCode)
            && $line !== null
            && isset($metaEditRestrictions[$attributeCode][$line][$name])
        ) {
            return $metaEditRestrictions[$attributeCode][$line][$name];
        } elseif (isset($metaEditRestrictions[$attributeCode][$name])) {
            return $metaEditRestrictions[$attributeCode][$name];
        }
        return true;
    }

    /**
     * Check if attribute is multiline
     *
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isMultiline($attributeCode)
    {
        return $this->getMultilineCount($attributeCode) > 1;
    }

    /**
     * Get multiline count
     *
     * @param string $attributeCode
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMultilineCount($attributeCode)
    {
        $metadata = $this->addressMetadata->getAttributeMetadata($attributeCode);
        return $metadata->getMultilineCount();
    }

    /**
     * Get metadata fields with boolean values
     *
     * @return array
     */
    public function getBooleanMetaFields()
    {
        return $this->booleanMetaFields;
    }

    /**
     * Get base html Id
     *
     * @return string
     */
    public function getHtmlId()
    {
        $htmlId = $this->getData('html_id');
        if (!$htmlId) {
            $htmlId = '_' . uniqid();
            $this->setData('html_id', $htmlId);
        }
        return $htmlId;
    }

    /**
     * Get input html Id
     *
     * @param string $attributeCode
     * @param string $metaField
     * @param int|null $line
     * @return string
     */
    public function getInputHtmlId($attributeCode, $metaField, $line = null)
    {
        $htmlId = $this->getHtmlId() . '-attribute-' . $attributeCode;
        if ($this->isMultiline($attributeCode) && $line !== null) {
            $htmlId .= '-' . $line;
        }
        $htmlId .= '-' . $metaField;
        return $htmlId;
    }

    /**
     * Get input html name
     *
     * @param string $attributeCode
     * @param string $metaField
     * @param int|null $line
     * @param string $part
     * @return string
     */
    public function getInputHtmlName($attributeCode, $metaField, $line = null, $part = 'attributes')
    {
        $htmlName = $this->getElement()->getName() . '[' . $part . '][' . $attributeCode . ']';
        if ($this->isMultiline($attributeCode) && $line !== null) {
            $htmlName .= '[' . $line . ']';
        }
        $htmlName .= '[' . $metaField . ']';
        return $htmlName;
    }
}
