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




































































































































































namespace Aheadworks\AdvancedReports\Model\Source\Email;

use Aheadworks\AdvancedReports\Model\Source\Period;

/**
 * Class Frequency
 *
 * @package Aheadworks\AdvancedReports\Model\Source\Email
 */
class Frequency extends Period
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_LAST_MONTH, 'label' => __('On The First Day Of A Month')],
            ['value' => self::TYPE_LAST_WEEK, 'label' => __('On The First Day Of A Week')],
            ['value' => self::TYPE_LAST_QUARTER, 'label' => __('On The First Day Of A Quarter')]
        ];
    }
}
