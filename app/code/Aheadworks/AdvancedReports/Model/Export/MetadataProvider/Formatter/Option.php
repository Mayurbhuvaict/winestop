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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Option
 *
 * @package Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter
 */
class Option implements FormatterInterface
{
    /**
     * Formatter type
     */
    const TYPE = 'option';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ComponentInspector
     */
    private $componentInspector;

    /**
     * @param Filter $filter
     * @param ComponentInspector $componentInspector
     */
    public function __construct(
        Filter $filter,
        ComponentInspector $componentInspector
    ) {
        $this->filter = $filter;
        $this->componentInspector = $componentInspector;
    }

    /**
     * @inheritdoc
     */
    public function format($field, $value)
    {
        $options = $this->getOptions($field);
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return $value;
    }

    /**
     * Get options
     *
     * @param string $field
     * @return array
     * @throws LocalizedException
     */
    private function getOptions($field)
    {
        $component = $this->filter->getComponent();
        $options = $this->componentInspector->getOptions($component, $field);

        return $options;
    }
}
