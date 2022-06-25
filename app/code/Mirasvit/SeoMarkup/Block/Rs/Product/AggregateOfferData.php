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



namespace Mirasvit\SeoMarkup\Block\Rs\Product;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Shipping\Model\Config as ShippingConfig;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Locale\FormatInterface;

class AggregateOfferData
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var FormatInterface
     */
    private $formatInterface;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var ProductConfig
     */
    private $productConfig;

    /**
     * @var TemplateEngineServiceInterface
     */
    private $templateEngineService;

    /**
     * @var PaymentConfig
     */
    private $paymentConfig;

    /**
     * @var OfferData
     */
    private $offerData;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ShippingConfig
     */
    private $shippingConfig;

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * AggregateOfferData constructor.
     * @param ProductConfig $productConfig
     * @param OfferData $offerData
     * @param TemplateEngineServiceInterface $templateEngineService
     * @param PaymentConfig $paymentConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param ShippingConfig $shippingConfig
     * @param ProductRepository $productRepository
     * @param LayoutInterface $layout
     * @param FormatInterface $formatInterface
     */
    public function __construct(
        ProductConfig $productConfig,
        OfferData $offerData,
        TemplateEngineServiceInterface $templateEngineService,
        PaymentConfig $paymentConfig,
        ScopeConfigInterface $scopeConfig,
        ShippingConfig $shippingConfig,
        ProductRepository $productRepository,
        LayoutInterface $layout,
        FormatInterface $formatInterface
    ) {
        $this->productConfig         = $productConfig;
        $this->offerData             = $offerData;
        $this->templateEngineService = $templateEngineService;
        $this->paymentConfig         = $paymentConfig;
        $this->scopeConfig           = $scopeConfig;
        $this->shippingConfig        = $shippingConfig;
        $this->productRepository     = $productRepository;
        $this->layout                = $layout;
        $this->formatInterface       = $formatInterface;
    }

    /**
     * @param object $product
     * @param object $store
     *
     * @return array|false
     */
    public function getData($product, $store)
    {
        $this->product = $product;
        $this->store   = $store;

        $values = [
            '@type'         => 'AggregateOffer',
            'lowPrice'      => 0,
            'highPrice'     => 0,
            'priceCurrency' => $this->store->getCurrentCurrencyCode(),
            'offers'        => [],
        ];

        $minPrice = 0;
        $maxPrice = 0;

        $type         = $this->product->getTypeId();
        $typeInstance = $this->product->getTypeInstance();

        switch ($type) {
            case 'configurable':
                $child = $typeInstance->getUsedProductCollection($this->product)
                    ->addAttributeToSelect('visibility')
                    ->addPriceData();

                foreach ($child as $item) {
                    $offer = $this->offerData->getData($item, $this->store);
                    if (!$offer) {
                        continue;
                    }

                    $minPrice = $minPrice == 0 ? $offer['price'] : min($minPrice, $offer['price']);
                    $maxPrice = max($maxPrice, $offer['price']);

                    $values['offers'][] = $offer;
                }

                if (empty($values['offers'])) {
                    $values['offers'][] = $this->getOutOfStockOffer();
                }

                break;
            case 'grouped':
                $childrenIds = $typeInstance->getChildrenIds($this->product->getId());
                foreach (array_values($childrenIds)[0] as $childId) {
                    $offer = $this->offerData->getData($this->productRepository->getById($childId), $this->store);
                    if (!$offer) {
                        continue;
                    }

                    $minPrice = $minPrice == 0 ? $offer['price'] : min($minPrice, $offer['price']);
                    $maxPrice = max($maxPrice, $offer['price']);

                    $values['offers'][] = $offer;
                }

                if (empty($values['offers'])) {
                    $values['offers'][] = $this->getOutOfStockOffer();
                }

                break;
            default:
                $offer = $this->offerData->getData($this->product, $this->store);
                if (!$offer) {
                    break;
                }

                $minPrice           = $minPrice == 0 ? $offer['price'] : min($minPrice, $offer['price']);
                $maxPrice           = max($maxPrice, $offer['price']);
                $values['offers'][] = $offer;
                $priceData          = $this->product->getPriceInfo()->getPrice('final_price');
                $minPrice           = $priceData->getMinimalPrice()->__toString();
                $maxPrice           = $priceData->getMaximalPrice()->__toString();

                break;
        }

        if (!$minPrice) {
            $minPrice = strip_tags(html_entity_decode($this->getPrice($this->product)));
            preg_match_all('/[0-9\.\,]+/', $minPrice, $matches);

            if (isset($matches[0][0])) {
                $minPrice = $matches[0][0];
            }
        }

        $minPrice = $this->formatInterface->getNumber($minPrice);
        $minPrice = number_format($minPrice, 2, '.', '');

        $values['lowPrice']   = $minPrice;
        $values['highPrice']  = $maxPrice;
        $values['offerCount'] = count($values['offers']);

        if (!$values['lowPrice'] || !$values['offerCount']) {
            return $this->offerData->getData($product, $store);
        }

        return $values;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getPrice($product)
    {
        $priceRender = $this->layout->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->layout->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = '';
        if ($priceRender) {
            /** @var mixed $priceRender */
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price'  => true,
                    'use_link_for_as_low_as' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }

    /**
     * @return array
     */
    private function getOutOfStockOffer()
    {
        return [
            '@type'                   => 'Offer',
            'availability'            => 'http://schema.org/OutOfStock',
        ];
    }
}
