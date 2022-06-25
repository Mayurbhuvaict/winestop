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



namespace Mirasvit\SeoContent\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\SeoContent\Model\Config;

class PageNumberInMetaSource implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            [
                'value' => Config::PAGE_NUMBER_POSITION_AT_BEGIN,
                'label' => __('At the beginning'),
            ],
            [
                'value' => Config::PAGE_NUMBER_POSITION_AT_END,
                'label' => __('At the end'),
            ],
            [
                'value' => Config::PAGE_NUMBER_POSITION_AT_BEGIN_WITH_FIRST,
                'label' => __('At the beginning (add to the first page)'),
            ],
            [
                'value' => Config::PAGE_NUMBER_POSITION_AT_END_WITH_FIRST,
                'label' => __('At the end (add to the first page)'),
            ],
        ];
    }
}
