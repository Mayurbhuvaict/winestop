<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree;

/**
 * Interface StatusInterface
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree
 */
interface StatusInterface
{
    /**
     * Check if context mode is available
     *
     * @return bool
     */
    public function isPayPalInContextMode();
}
