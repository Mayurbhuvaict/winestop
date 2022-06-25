<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\MspReCaptcha as MspReCaptchaStatus;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\OneStepCheckout\Model\Layout\DefinitionFetcher;

/**
 * Class PaypalReCaptcha
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class PaypalReCaptcha implements LayoutProcessorInterface
{
    /**
     * @var MspReCaptchaStatus
     */
    private $mspReCaptchaStatus;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var DefinitionFetcher
     */
    private $definitionFetcher;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param MspReCaptchaStatus $mspReCaptchaStatus
     * @param ArrayManager $arrayManager
     * @param DefinitionFetcher $definitionFetcher
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        MspReCaptchaStatus $mspReCaptchaStatus,
        ArrayManager $arrayManager,
        DefinitionFetcher $definitionFetcher,
        ObjectManagerInterface $objectManager
    ) {
        $this->mspReCaptchaStatus = $mspReCaptchaStatus;
        $this->arrayManager = $arrayManager;
        $this->definitionFetcher = $definitionFetcher;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if ($this->mspReCaptchaStatus->isEnabled()) {
            if (isset($jsLayout['components']['checkout']['children']['paymentMethod']['children']['methodList']['children'])) {
                $this->addChildrenPayPalFields(
                    $jsLayout['components']['checkout']['children']['paymentMethod']['children']['methodList']['children']
                );
            }
            $path = [
                'toOur' => 'components/checkout/children/paymentMethod/children/methodList/children/'
                    . 'paypal-captcha/children/msp_recaptcha',
                'fromNative' => 'components/checkout/children/steps/children/billing-step/children/'
                    . 'payment/children/payments-list/children/paypal-captcha/children/msp_recaptcha'
            ];
            $payPalJsLayout = $this->getLayoutProcessor()->process([]);
            $ourLayout = $this->arrayManager->get($path['toOur'], $jsLayout);
            $nativeLayout = $this->arrayManager->get($path['fromNative'], $payPalJsLayout);
            if ($ourLayout && $nativeLayout) {
                $jsLayout = $this->arrayManager->merge($path['toOur'], $jsLayout, $nativeLayout);
            } elseif ($ourLayout && !$nativeLayout) {
                $jsLayout = $this->arrayManager->remove($path['toOur'], $jsLayout);
            }
        }

        return $jsLayout;
    }

    /**
     * Add additional fields
     *
     * @param array $layout
     * @return void
     */
    private function addChildrenPayPalFields(array &$layout)
    {
        $path = '//referenceBlock[@name="checkout.root"]/arguments/argument[@name="jsLayout"]'
            . '/item[@name="components"]/item[@name="checkout"]/item[@name="children"]'
            . '/item[@name="steps"]/item[@name="children"]/item[@name="billing-step"]'
            . '/item[@name="children"]/item[@name="payment"]/item[@name="children"]'
            . '/item[@name="payments-list"]/item[@name="children"]';
        $definitions = $this->definitionFetcher->fetchArgs('checkout_index_index', $path);
        $layout = array_merge_recursive($layout, $definitions);
    }
    
    /**
     * Retrieve PayPal ReCaptcha layout processor
     *
     * @return \Magento\PaypalReCaptcha\Block\LayoutProcessor\Checkout\Onepage
     */
    private function getLayoutProcessor()
    {
        return $this->objectManager->get(\Magento\PaypalReCaptcha\Block\LayoutProcessor\Checkout\Onepage::class);
    }
}
