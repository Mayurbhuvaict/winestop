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

use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\AdvancedReports\Ui\Component\Inspector as ComponentInspector;
use Magento\Ui\Component\Listing\Columns\Date as DateColumn;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Date
 *
 * @package Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter
 */
class Date implements FormatterInterface
{
    /**
     * Formatter type
     */
    const TYPE = 'date';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ComponentInspector
     */
    private $componentInspector;

    /**
     * @param Filter $filter
     * @param ComponentInspector $componentInspector
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Filter $filter,
        ComponentInspector $componentInspector,
        TimezoneInterface $timezone
    ) {
        $this->filter = $filter;
        $this->componentInspector = $componentInspector;
        $this->timezone = $timezone;
    }

    /**
     * @inheritdoc
     */
    public function format($field, $value)
    {
        $component = $this->filter->getComponent();
        $column = $this->componentInspector->getColumnObject($component, $field);
        if ($column && $column instanceof DateColumn) {
            $value = $this->timezone->formatDateTime(
                $value,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::MEDIUM,
                $column->getData('config/storeLocale'),
                $column->getData('config/storeTimeZone')
            );
        }

        return $value;
    }
}
