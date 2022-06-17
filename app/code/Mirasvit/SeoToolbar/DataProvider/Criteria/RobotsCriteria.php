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

class RobotsCriteria extends AbstractCriteria
{
    const LABEL = 'Robots Meta';

    /**
     * @param string $content
     * @return \Magento\Framework\DataObject
     */
    public function handle($content)
    {
        $value = $this->getMetaTag($content, 'robots');

        if (!$value) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_NONE,
                __('Is not set'),
                $value
            );
        }

        return $this->getItem(
            self::LABEL,
            DataProviderItemInterface::STATUS_NONE,
            null,
            $value
        );
    }
}
