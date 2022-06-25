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



namespace Mirasvit\SeoMarkup\Model\Config\Source\Product;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class DescriptionType implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => ProductConfig::DESCRIPTION_TYPE_DESCRIPTION, 'label' => __('Use description')],
            ['value' => ProductConfig::DESCRIPTION_TYPE_META, 'label' => __('Use meta description')],
            ['value' => ProductConfig::DESCRIPTION_TYPE_SHORT_DESCRIPTION, 'label' => __('Use short description')],
        ];
    }
}
