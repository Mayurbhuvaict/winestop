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

use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class OrderStatus
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class OrderStatus implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var OrderConfig
     */
    private $orderConfig;

    /**
     * @param OrderConfig $orderConfig
     */
    public function __construct(OrderConfig $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $statuses = $this->orderConfig->getStatuses();
            $options = [];

            foreach ($statuses as $code => $label) {
                $options[] = ['value' => $code, 'label' => $label];
            }
            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->toOptionArray();
        $result = [];

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }

    /**
     * Get option by value
     *
     * @param int $value
     * @return null
     */
    public function getOptionByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}
