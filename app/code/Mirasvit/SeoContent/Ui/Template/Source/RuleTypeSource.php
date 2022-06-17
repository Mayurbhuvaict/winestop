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



namespace Mirasvit\SeoContent\Ui\Template\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;

class RuleTypeSource implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Categories'),
                'value' => TemplateInterface::RULE_TYPE_CATEGORY,
            ],
            [
                'label' => __('Layered Navigation'),
                'value' => TemplateInterface::RULE_TYPE_NAVIGATION,
            ],
            [
                'label' => __('Products'),
                'value' => TemplateInterface::RULE_TYPE_PRODUCT,
            ],
            [
                'label' => __('CMS Pages'),
                'value' => TemplateInterface::RULE_TYPE_PAGE,
            ],
        ];
    }
}
