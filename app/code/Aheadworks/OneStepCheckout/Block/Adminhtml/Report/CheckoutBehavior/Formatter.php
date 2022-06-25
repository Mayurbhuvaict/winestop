<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior;

/**
 * Class Formatter
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior
 */
class Formatter
{
    /**
     * Format value in percents
     *
     * @param float $value
     * @return string
     */
    public function formatPercents($value)
    {
        return number_format($value, 2) . '%';
    }
}
