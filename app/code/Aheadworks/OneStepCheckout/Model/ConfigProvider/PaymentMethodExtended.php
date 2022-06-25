<?php
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Braintree\Model\Ui\PayPal\ConfigProvider as PayPalConfigProvider;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree\Status as BraintreeModuleStatus;

/**
 * Class PaymentMethodExtended
 *
 * @package Aheadworks\OneStepCheckout\Model\ConfigProvider
 */
class PaymentMethodExtended implements ConfigProviderInterface
{
    /**
     * @var BraintreeModuleStatus
     */
    private $braintreeModuleStatus;

    /**
     * @param BraintreeModuleStatus $braintreeModuleStatus
     */
    public function __construct(BraintreeModuleStatus $braintreeModuleStatus)
    {
        $this->braintreeModuleStatus = $braintreeModuleStatus;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                PayPalConfigProvider::PAYPAL_CODE => [
                    'awOscIsContextCheckout' => $this->braintreeModuleStatus->isPayPalInContextMode()
                ]
            ]
        ];
    }
}
