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



namespace Mirasvit\SeoToolbar\DataProvider\Criteria;

use Mirasvit\SeoToolbar\Api\Data\DataProviderItemInterface;

class TitleCriteria extends AbstractCriteria
{
    const LABEL = 'Title';

    /**
     * @param string $content
     * @return \Magento\Framework\DataObject
     */
    public function handle($content)
    {
        $value = $this->getMetaTag($content, 'title');

        $len = mb_strlen($value);
        if ($len < 10) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_WARNING,
                __('%1 characters — not good. Try to enlarge it to 10 characters.', $len),
                $value
            );
        }

        if ($len > 70) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_WARNING,
                __('%1 characters — not good. Try to minimize it to 70 characters.', $len),
                $value
            );
        }

        return $this->getItem(
            self::LABEL,
            DataProviderItemInterface::STATUS_SUCCESS,
            __('%1 characters — optimal.', $len),
            $value
        );
    }
}
