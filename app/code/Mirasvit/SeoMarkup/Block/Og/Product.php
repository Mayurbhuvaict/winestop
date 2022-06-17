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

namespace Mirasvit\SeoMarkup\Block\Og;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Directory\Block\Currency;
use Magento\Framework\View\Element\Template;
use Mirasvit\Seo\Api\Service\StateServiceInterface;

class Product extends AbstractBlock
{
    private $imageHelper;

    private $currency;

    private $outputHelper;

    private $stateService;

    public function __construct(
        ImageHelper $imageHelper,
        Currency $currency,
        OutputHelper $outputHelper,
        StateServiceInterface $stateService,
        Template\Context $context
    ) {
        $this->imageHelper  = $imageHelper;
        $this->currency     = $currency;
        $this->outputHelper = $outputHelper;
        $this->stateService = $stateService;

        parent::__construct($context);
    }

    protected function getMeta(): ?array
    {
        $product = $this->stateService->getProduct();

        if (!$product) {
            return null;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();

        $priceAmount = $product->getPriceInfo()
            ->getPrice(FinalPrice::PRICE_CODE)
            ->getAmount();

        $meta = [
            'og:type'                => 'product',
            'og:url'                 => $this->_urlBuilder->escape($product->getProductUrl()),
            'og:title'               => $this->pageConfig->getTitle()->get(),
            'og:description'         => $this->outputHelper->productAttribute(
                $product,
                $product->getData('short_description'),
                'og:short_description'
            ),
            'og:image'               => $this->getImageUrl($product),
            'og:site_name'           => $store->getFrontendName(),
            'product:price:amount'   => $priceAmount,
            'product:price:currency' => $this->currency->getCurrentCurrencyCode(),
        ];

        return $meta;
    }

    protected function getImageUrl(ProductInterface $product): string
    {
        return $this->imageHelper->init($product, 'product_base_image')
            ->keepAspectRatio(true)
            ->resize(800)
            ->getUrl();
    }
}
