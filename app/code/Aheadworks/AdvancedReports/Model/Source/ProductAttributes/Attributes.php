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









































































































































































namespace Aheadworks\AdvancedReports\Model\Source\ProductAttributes;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Attributes
 *
 * @package Aheadworks\AdvancedReports\Model\Source\ProductAttributes
 */
class Attributes implements OptionSourceInterface
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get options
     *
     * @return []
     */
    public function toOptionArray()
    {
        $allowedAttributes = [];
        $attributes = $this->productAttributeRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
        foreach ($attributes as $attribute) {
            /* @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            if (!$attribute->isAllowedForRuleCondition() ||
                !$attribute->getDataUsingMethod('is_used_for_promo_rules') ||
                $attribute->getAttributeCode() == 'category_ids'
            ) {
                continue;
            }
            $allowedAttributes[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel(),
                'type' => $attribute->getFrontendInput(),
                'conditions' => $this->getConditionsByType($attribute->getFrontendInput()),
                'options' => $attribute->getSource()->getAllOptions(false, false)
            ];
        }
        uasort($allowedAttributes, function ($firstItem, $secondItem) {
            return $firstItem['label'] > $secondItem['label'];
        });
        return $allowedAttributes;
    }

    /**
     * Retrieve conditions for attribute type
     *
     * @param string $attributeType
     * @return []
     */
    private function getConditionsByType($attributeType)
    {
        $conditions = [
            ['value' => 'eq', 'label' => __('is')],
            ['value' => 'neq', 'label' => __('is not')]
        ];

        if (in_array($attributeType, ['multiselect'])) {
            $conditions = [
                ['value' => 'finset', 'label' => __('contains')],
                ['value' => 'not_finset', 'label' => __('does not contain')]
            ];
        }
        if (in_array($attributeType, ['text', 'textarea'])) {
            $conditions[] = ['value' => 'like', 'label' => __('contains')];
            $conditions[] = ['value' => 'nlike', 'label' => __('does not contain')];
        }
        if (in_array($attributeType, ['date', 'price'])) {
            $conditions[] = ['value' => 'gteq', 'label' => __('equals or greater than')];
            $conditions[] = ['value' => 'lteq', 'label' => __('equals or less than')];
            $conditions[] = ['value' => 'gt', 'label' => __('greater than')];
            $conditions[] = ['value' => 'lt', 'label' => __('less than')];
        }
        if (in_array($attributeType, ['text', 'textarea', 'price'])) {
            $conditions[] = ['value' => 'in', 'label' => __('is one of')];
            $conditions[] = ['value' => 'nin', 'label' => __('is not one of')];
        }

        return $conditions;
    }

    /**
     * Retrieve options count
     *
     * @return int
     */
    public function getOptionCount()
    {
        return count($this->toOptionArray());
    }
}
