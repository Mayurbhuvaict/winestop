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


















































































































































































namespace Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter;

/**
 * Class Percent
 *
 * @package Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter
 */
class Percent implements FormatterInterface
{
    /**
     * Formatter type
     */
    const TYPE = 'percent';

    /**
     * @inheritdoc
     */
    public function format($field, $value)
    {
        if ($value === null) {
            $value = 0;
        }

        $value = $value * 1;
        return $value . '%';
    }
}
