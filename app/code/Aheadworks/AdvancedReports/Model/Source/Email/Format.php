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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Format
 *
 * @package Aheadworks\AdvancedReports\Model\Source\Email
 */
class Format implements OptionSourceInterface
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_CSV = 'csv';
    const TYPE_EXCEL_XML = 'xml';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_CSV, 'label' => __('CSV')],
            ['value' => self::TYPE_EXCEL_XML, 'label' => __('Excel XML')]
        ];
    }
}
