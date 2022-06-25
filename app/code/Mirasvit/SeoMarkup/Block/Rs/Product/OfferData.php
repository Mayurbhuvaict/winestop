<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoMarkup\Block\Rs\Product;

use Magento\Catalog\Helper\Data as TaxHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\Store;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class OfferData
{
    const PRODUCT_PRICES_INCLUDING_TAX = 2;

    /** @var Product */
    private $product;

    /** @var Store */
    private $store;

    private $productConfig;

    private $templateEngineService;

    private $paymentConfig;

    private $scopeConfig;

    private $shippingConfig;

    private $taxHelper;

    public function __construct(
        ProductConfig                  $productConfig,
        TemplateEngineServiceInterface $templateEngineService,
        PaymentConfig                  $paymentConfig,
        ScopeConfigInterface           $scopeConfig,
        ShippingConfig                 $shippingConfig,
        TaxHelper                      $taxHelper
    ) {
        $this->productConfig         = $productConfig;
        $this->templateEngineService = $templateEngineService;
        $this->paymentConfig         = $paymentConfig;
        $this->scopeConfig           = $scopeConfig;
        $this->shippingConfig        = $shippingConfig;
        $this->taxHelper             = $taxHelper;
    }

    public function getData(Product $product, Store $store, bool $dry = false): array
    {
        $this->product = $dry ? $product : $product->load($product->getId());
        $this->store   = $store;

        $currencyCode = $this->store->getCurrentCurrencyCode();
        $finalPrice   = $this->getFinalPrice();

        $values = [
            '@type'                   => 'Offer',
            'url'                     => $this->product->getVisibility() != 1 ? $this->product->getProductUrl() : false,
            'price'                   => number_format($finalPrice, 2, '.', ''),
            'priceCurrency'           => $currencyCode,
            'priceValidUntil'         => $this->getPriceValidUntil(),
            'availability'            => $this->getOfferAvailability(),
            'itemCondition'           => $this->getOfferItemCondition(),
            'acceptedPaymentMethod'   => $this->getOfferAcceptedPaymentMethods(),
            'availableDeliveryMethod' => $this->getOfferAvailableDeliveryMethods(),
            'sku'                     => $this->product->getSku(),
            'gtin'                    => $this->getGtin(),
        ];

        $data = [];
        foreach ($values as $key => $value) {
            if ($value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    protected function getOfferAvailableDeliveryMethods(): ?array
    {
        if (!$this->productConfig->isAvailableDeliveryMethodEnabled()) {
            return null;
        }

        if ($activeDeliveryMethods = $this->getActiveDeliveryMethods()) {
            return $activeDeliveryMethods;
        }

        return null;
    }

    private function getOfferAvailability(): ?string
    {
        if (!$this->productConfig->isAvailabilityEnabled()) {
            return null;
        }

        $productAvailability = method_exists($this->product, 'isAvailable')
            ? $this->product->isAvailable()
            : $this->product->isInStock();

        if ($productAvailability) {
            return "http://schema.org/InStock";
        } else {
            return "http://schema.org/OutOfStock";
        }
    }

    private function getOfferItemCondition(): ?string
    {
        $conditionType = $this->productConfig->getItemConditionType();

        if (!$conditionType) {
            return null;
        }

        if ($conditionType == ProductConfig::ITEM_CONDITION_NEW_ALL) {
            return "http://schema.org/NewCondition";
        } elseif ($conditionType == ProductConfig::ITEM_CONDITION_MANUAL) {
            $attribute      = $this->productConfig->getItemConditionAttribute();
            $conditionValue = $this->templateEngineService->render("[product_$attribute]");

            if (!$conditionValue) {
                return null;
            }

            switch ($conditionValue) {
                case $this->productConfig->getItemConditionAttributeValueNew():
                    return "http://schema.org/NewCondition";

                case $this->productConfig->getItemConditionAttributeValueUsed():
                    return "http://schema.org/UsedCondition";

                case $this->productConfig->getItemConditionAttributeValueRefurbished():
                    return "http://schema.org/RefurbishedCondition";

                case $this->productConfig->getItemConditionAttributeValueDamaged():
                    return "http://schema.org/DamagedCondition";
            }
        }

        return null;
    }

    private function getOfferAcceptedPaymentMethods(): ?array
    {
        if (!$this->productConfig->isAcceptedPaymentMethodEnabled()) {
            return null;
        }

        if ($activePaymentMethods = $this->getActivePaymentMethods()) {
            return $activePaymentMethods;
        }

        return null;
    }

    private function getActivePaymentMethods(): array
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods  = [];
        foreach (array_keys($payments) as $paymentCode) {
            if (strpos($paymentCode, 'paypal') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#PayPal';
            }

            if (strpos($paymentCode, 'googlecheckout') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#GoogleCheckout';
            }

            if (strpos($paymentCode, 'cash') !== false) {
                $methods[] = 'http://purl.org/goodrelations/v1#Cash';
            }

            if ($paymentCode == 'ccsave') {
                if ($existingMethods = $this->getActivePaymentCCTypes()) {
                    $methods = array_merge($methods, $existingMethods);
                }
            }
        }

        return array_unique($methods);
    }

    private function getActivePaymentCCTypes(): ?array
    {
        $methods    = [];
        $allMethods = [
            'AE'  => 'AmericanExpress',
            'VI'  => 'VISA',
            'MC'  => 'MasterCard',
            'DI'  => 'Discover',
            'JCB' => 'JCB',
        ];

        $ccTypes = $this->scopeConfig->getValue(
            'payment/ccsave/cctypes',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->store
        );

        if ($ccTypes) {
            $list = explode(',', $ccTypes);

            foreach ($list as $value) {
                if (isset($allMethods[$value])) {
                    $methods[] = 'http://purl.org/goodrelations/v1#' . $allMethods[$value];
                }
            }

            return $methods;
        }

        return null;
    }

    private function getActiveDeliveryMethods(): array
    {
        $methods = [];

        $allMethods = [
            'flatrate'     => 'DeliveryModeFreight',
            'freeshipping' => 'DeliveryModeFreight',
            'tablerate'    => 'DeliveryModeFreight',
            'dhl'          => 'DHL',
            'fedex'        => 'FederalExpress',
            'ups'          => 'UPS',
            'usps'         => 'DeliveryModeMail',
            'dhlint'       => 'DHL',
        ];

        $deliveryMethods = $this->shippingConfig->getActiveCarriers();
        foreach (array_keys($deliveryMethods) as $code) {
            if (isset($allMethods[$code])) {
                $methods[] = 'http://purl.org/goodrelations/v1#' . $allMethods[$code];
            }
        }

        return array_unique($methods);
    }

    private function getGtin(): string
    {
        return (string)$this->product->getData($this->productConfig->getGtin8Attribute());
    }

    private function getPriceValidUntil(): string
    {
        $specialToDate = $this->templateEngineService->render(
            '[product_special_to_date]',
            ['product' => $this->product]
        );

        if (strtotime($specialToDate) > time()) {
            return date("Y-m-d ", strtotime($specialToDate));
        } else {
            return '2030-01-01';
        }
    }

    private function getFinalPrice(): float
    {
        $taxesType = $this->scopeConfig->getValue(
            'tax/display/type',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->store
        );

        if ($taxesType == self::PRODUCT_PRICES_INCLUDING_TAX) {
            return (float)$this->taxHelper->getTaxPrice(
                $this->product,
                $this->product->getFinalPrice(),
                $includingTax = true
            );
        }

        return (float)$this->product->getPriceInfo()->getPrice('final_price')->getValue();
    }
}
