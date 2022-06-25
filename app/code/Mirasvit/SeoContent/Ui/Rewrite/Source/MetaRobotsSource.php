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



namespace Mirasvit\SeoContent\Ui\Rewrite\Source;

use Magento\Framework\Option\ArrayInterface;

class MetaRobotsSource implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '-', 'label' => __('Don\'t change')],
            ['value' => 'noindex,nofollow', 'label' => __('NOINDEX, NOFOLLOW')],
            ['value' => 'noindex,follow', 'label' => __('NOINDEX, FOLLOW')],
            ['value' => 'index,nofollow', 'label' => __('INDEX, NOFOLLOW')],
            ['value' => 'index,follow', 'label' => __('INDEX, FOLLOW')],
        ];
    }
}
