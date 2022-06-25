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






























































































































































namespace Aheadworks\AdvancedReports\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Compare
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class Compare implements OptionSourceInterface
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_PREVIOUS_PERIOD  = 'previous_period';
    const TYPE_PREVIOUS_YEAR = 'previous_year';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_PREVIOUS_PERIOD, 'label' => __('Previous period')],
            ['value' => self::TYPE_PREVIOUS_YEAR, 'label' => __('Previous year')],
        ];
    }
}
