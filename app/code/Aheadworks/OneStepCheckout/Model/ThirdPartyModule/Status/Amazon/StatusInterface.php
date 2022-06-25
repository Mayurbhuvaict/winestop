<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon;

/**
 * Interface StatusInterface
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon
 */
interface StatusInterface
{
    /**
     * Check if Amazon Payment Enabled
     *
     * @return bool
     */
    public function isPwaEnabled();
}
