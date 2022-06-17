<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Model\Template\Rule\Condition;

use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Model\Config;
use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var CategoryCondition
     */
    private $categoryCondition;

    /**
     * @var ProductCondition
     */
    private $productCondition;

    /**
     * @var string
     */
    private $ruleType;

    /**
     * Combine constructor.
     * @param CategoryCondition $categoryCondition
     * @param ProductCondition $productCondition
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CategoryCondition $categoryCondition,
        ProductCondition $productCondition,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->categoryCondition = $categoryCondition;
        $this->productCondition = $productCondition;

        $this->setData('type', self::class);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setRuleType($type)
    {
        $this->ruleType = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->productCondition->loadAttributeOptions()->getData('attribute_option');
        $categoryAttributes = $this->categoryCondition->loadAttributeOptions()->getData('attribute_option');

        $attributes = [];

        foreach ($productAttributes as $code => $label) {
            $attributes['product'][] = [
                'value' => ProductCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        foreach ($categoryAttributes as $code => $label) {
            $attributes['category'][] = [
                'value' => CategoryCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => self::class,
                'label' => __('Conditions Combination'),
            ],
        ]);

        if (in_array(
            $this->ruleType,
            [null, TemplateInterface::RULE_TYPE_CATEGORY, TemplateInterface::RULE_TYPE_NAVIGATION]
        )) {
            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => __('Category Attributes'),
                    'value' => $attributes['category'],
                ],
            ]);
        }

        if (in_array($this->ruleType, [null, TemplateInterface::RULE_TYPE_PRODUCT])) {
            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => __('Product Attributes'),
                    'value' => $attributes['product'],
                ],
            ]);
        }

        return $conditions;
    }

    /**
     * @param string $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
