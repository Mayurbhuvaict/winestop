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

namespace Mirasvit\Seo\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\Seo\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD)
 */
class Canonical implements ObserverInterface
{
    protected $config;

    protected $scopeConfig;

    protected $productTypeFactory;

    protected $categoryCollectionFactory;

    protected $productCollectionFactory;

    protected $context;

    protected $registry;

    protected $seoData;

    protected $storeManager;

    protected $request;

    protected $productTypeConfigurable;

    protected $productTypeBundle;

    protected $productTypeGrouped;

    protected $urlRewrite;

    protected $urlPrepare;

    private $productRepository;

    private $stateService;

    private $canonicalRewriteService;

    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Bundle\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Bundle\Model\Product\Type $productTypeBundle,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $productTypeGrouped,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite,
        \Mirasvit\Seo\Helper\UrlPrepare $urlPrepare,
        \Mirasvit\Seo\Api\Service\CanonicalRewrite\CanonicalRewriteServiceInterface $canonicalRewriteService,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        StateServiceInterface $stateService
    ) {
        $this->config                    = $config;
        $this->productTypeFactory        = $productTypeFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->productTypeConfigurable   = $productTypeConfigurable;
        $this->productTypeBundle         = $productTypeBundle;
        $this->productTypeGrouped        = $productTypeGrouped;
        $this->context                   = $context;
        $this->registry                  = $registry;
        $this->seoData                   = $seoData;
        $this->storeManager              = $context->getStoreManager();
        $this->request                   = $context->getRequest();
        $this->urlRewrite                = $urlRewrite;
        $this->urlPrepare                = $urlPrepare;
        $this->canonicalRewriteService   = $canonicalRewriteService;
        $this->productRepository         = $productRepository;
        $this->stateService              = $stateService;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $this->setupCanonicalUrl();
    }

    public function setupCanonicalUrl(): void
    {
        if ($this->seoData->isIgnoredActions()
            && !$this->seoData->cancelIgnoredActions()) {
            return;
        }

        if ($canonicalUrl = $this->getCanonicalUrl()) {
            $this->addLinkCanonical($canonicalUrl);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity) 
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCanonicalUrl(): ?string
    {
        if (!$this->config->isAddCanonicalUrl() || $this->isIgnoredCanonical()) {
            return null;
        }

        if ($canonicalRewrite = $this->getCanonicalRewrite()) {
            return $canonicalRewrite;
        }

        $productActions = [
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        ];

        $productCanonicalStoreId = false;
        $useCrossDomain          = true;

        if (in_array($this->seoData->getFullActionCode(), $productActions)) {
            $product = $this->registry->registry('current_product');

            if (!$product) {
                return null;
            }

            $currentProductId    = $product->getId();
            $associatedProductId = $this->getAssociatedProductId($product);
            $productId           = ($associatedProductId) ? $associatedProductId : $product->getId();

            $productCanonicalStoreId       = $product->getSeoCanonicalStoreId(); //canonical store id for current product
            $canonicalUrlForCurrentProduct = trim((string)$product->getSeoCanonicalUrl());

            $collection = $this->productCollectionFactory->create()
                ->addFieldToFilter('entity_id', $productId)
                ->addStoreFilter()
                ->addUrlRewrite();

            $collection->setFlag('has_stock_status_filter');

            $product      = $collection->getFirstItem();
            $canonicalUrl = $product->getProductUrl();

            if ($this->config->isAddLongestCanonicalProductUrl()
                && $this->config->isProductLongUrlEnabled((int)$this->storeManager->getStore()->getId())
            ) {
                $canonicalUrl = $this->getLongestProductUrl($product, $canonicalUrl);
            }

            if ($canonicalUrlForCurrentProduct) {
                if (strpos($canonicalUrlForCurrentProduct, 'http://') !== false
                    || strpos($canonicalUrlForCurrentProduct, 'https://') !== false
                ) {
                    $canonicalUrl   = $canonicalUrlForCurrentProduct;
                    $useCrossDomain = false;
                } else {
                    $canonicalUrlForCurrentProduct = (substr(
                            $canonicalUrlForCurrentProduct,
                            0,
                            1
                        ) == '/') ? substr($canonicalUrlForCurrentProduct, 1) : $canonicalUrlForCurrentProduct;
                    $canonicalUrl                  = $this->context->getUrlBuilder()->getBaseUrl() . $canonicalUrlForCurrentProduct;
                }
            }
            $productLoaded = $this->productRepository->getById(
                $currentProductId,
                false,
                $this->storeManager->getStore()->getId()
            );

            //use custom canonical from products
            if ($productLoaded->getMSeoCanonical()) {
                $canonicalUrl = trim((string)$productLoaded->getMSeoCanonical());
                if (strpos($canonicalUrl, '://') === false) {
                    $canonicalUrl = $this->storeManager->getStore()->getBaseUrl() . ltrim($canonicalUrl, '/');
                }
            }
        } elseif ($this->seoData->getFullActionCode() == 'catalog_category_view') {
            $category = $this->registry->registry('current_category');
            if (!$category) {
                return null;
            }
            $canonicalUrl = $category->getUrl();
            $canonicalUrl = strtok($canonicalUrl, '?');
        } else {
            $canonicalUrl              = $this->seoData->getBaseUri();
            $preparedCanonicalUrlParam = ($this->config->isAddStoreCodeToUrlsEnabled() && $this->stateService->isHomePage()) || !$canonicalUrl
                ? ''
                : ltrim($canonicalUrl, '/');
            $canonicalUrl              = $this->context->getUrlBuilder()->getUrl('', ['_direct' => $preparedCanonicalUrlParam]);
            $canonicalUrl              = strtok($canonicalUrl, '?');
        }

        if ($this->config->getCanonicalStoreWithoutStoreCode((int)$this->storeManager->getStore()->getId())) {
            $storeCode    = $this->storeManager->getStore()->getCode();
            $canonicalUrl = str_replace('/' . $storeCode . '/', '/', $canonicalUrl);
            //setup crossdomian URL if this option is enabled
        } elseif ((($crossDomainStore = $this->config->getCrossDomainStore((int)$this->storeManager->getStore()->getId()))
                || $productCanonicalStoreId)
            && $useCrossDomain) {
            if ($productCanonicalStoreId) {
                $crossDomainStore = $productCanonicalStoreId;
            }
            $mainBaseUrl    = $this->storeManager->getStore($crossDomainStore)->getBaseUrl();
            $currentBaseUrl = $this->storeManager->getStore()->getBaseUrl();
            $canonicalUrl   = str_replace($currentBaseUrl, $mainBaseUrl, $canonicalUrl);

            $mainSecureBaseUrl = $this->storeManager->getStore($crossDomainStore)
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true);

            if ($this->storeManager->getStore()->isCurrentlySecure()
                || ($this->config->isPreferCrossDomainHttps()
                    && strpos($mainSecureBaseUrl, 'https://') !== false)) {
                $canonicalUrl = str_replace('http://', 'https://', $canonicalUrl);
            }
        }

        $canonicalUrl = $this->urlPrepare->deleteDoubleSlash($canonicalUrl);

        $page = (int)$this->request->getParam('p');
        if ($page > 1 && $this->config->isPaginatedCanonical()) {
            $canonicalUrl .= "?p=$page";
        }

        $canonicalUrl = $this->getPreparedTrailingCanonical($canonicalUrl);

        return $canonicalUrl;
    }

    /**
     * Check if canonical is ignored.
     */
    public function isIgnoredCanonical(): bool
    {
        $isIgnored = false;

        if (!$this->seoData->getFullActionCode() || $this->seoData->getFullActionCode() == '__') {
            return true;
        }

        foreach ($this->config->getCanonicalUrlIgnorePages() as $page) {
            if ($this->seoData->checkPattern($this->seoData->getFullActionCode(), $page)
                || $this->seoData->checkPattern($this->seoData->getBaseUri(), $page)) {
                $isIgnored = true;
            }
        }

        return $isIgnored;
    }

    public function getCanonicalRewrite(): ?string
    {
        if ($canonicalRewriteRule = $this->canonicalRewriteService->getCanonicalRewriteRule()) {
            return $canonicalRewriteRule->getData('canonical');
        }

        return null;
    }

    /**
     * Get associated product Id
     */
    protected function getAssociatedProductId(ProductInterface $product): ?int
    {
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
            return null;
        }

        $associatedProductId = null;

        if ($this->config->getAssociatedCanonicalConfigurableProduct()
            && ($parentConfigurableProductIds = $this
                ->productTypeConfigurable
                ->getParentIdsByChild($product->getId())
            )
            && isset($parentConfigurableProductIds[0])
            && $this->isProductEnabled((int)$parentConfigurableProductIds[0])) {
            $associatedProductId = (int)$parentConfigurableProductIds[0];
        }

        if (!$associatedProductId && $this->config->getAssociatedCanonicalGroupedProduct()
            && ($parentGroupedProductIds = $this
                ->productTypeGrouped
                ->getParentIdsByChild($product->getId())
            )
            && isset($parentGroupedProductIds[0])
            && $this->isProductEnabled((int)$parentGroupedProductIds[0])) {
            $associatedProductId = (int)$parentGroupedProductIds[0];
        }

        if (!$associatedProductId && $this->config->getAssociatedCanonicalBundleProduct()
            && ($parentBundleProductIds = $this
                ->productTypeBundle
                ->getParentIdsByChild($product->getId())
            )
            && isset($parentBundleProductIds[0])
            && $this->isProductEnabled((int)$parentBundleProductIds[0])) {
            $associatedProductId = (int)$parentBundleProductIds[0];
        }

        return $associatedProductId;
    }

    protected function isProductEnabled(int $id): bool
    {
        $product = $this->productRepository->getById(
            $id,
            false,
            $this->storeManager->getStore()->getId()
        );

        if ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
            return true;
        }

        return false;
    }

    protected function getLongestProductUrl(ProductInterface $product, string $canonicalUrl): string
    {
        $rewriteData = $this->urlRewrite->addFieldToFilter('entity_type', 'product')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->storeManager->getStore()->getId())
            ->addFieldToFilter('entity_id', $product->getId());

        if ($rewriteData && $rewriteData->getSize() > 1) {
            $urlPath = [];
            foreach ($rewriteData as $rewrite) {
                $requestPath             = $rewrite->getRequestPath();
                $requestPathExploded     = explode('/', $requestPath);
                $categoryCount           = count($requestPathExploded);
                $urlPath[$categoryCount] = $requestPath;
            }

            if ($urlPath) {
                $canonicalUrl = $this->storeManager->getStore()->getBaseUrl() . $urlPath[max(array_keys($urlPath))];
            }
        }

        return $canonicalUrl;
    }

    /**
     * Get Canonical with prepared Trailing slash (depending on Trailing slash config)
     */
    protected function getPreparedTrailingCanonical(string $canonicalUrl): string
    {
        $extension = substr(strrchr($canonicalUrl, '.'), 1);

        if ($this->config->getTrailingSlash() == Config::TRAILING_SLASH
            && substr($canonicalUrl, -1) != '/'
            && strpos($canonicalUrl, '?') === false
            && !in_array($extension, ['html', 'htm'])) {
            $canonicalUrl = $canonicalUrl . '/';
        } elseif ($this->config->getTrailingSlash() == Config::NO_TRAILING_SLASH
            && substr($canonicalUrl, -1) == '/') {
            if ($this->checkHomePageCanonical($canonicalUrl)) {
                return $canonicalUrl;
            } else {
                $canonicalUrl = substr($canonicalUrl, 0, -1);
            }
        }

        return $canonicalUrl;
    }

    protected function checkHomePageCanonical(string $canonicalUrl): bool
    {
        if ($this->stateService->isHomePage()
            && $this->config->isAddStoreCodeToUrlsEnabled()
            && $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)
            . $this->storeManager->getStore()->getCode()
            . '/' == $this->context->getUrlBuilder()->getCurrentUrl()
            && $this->context->getUrlBuilder()->getCurrentUrl() == $canonicalUrl) {
            return true;
        } else {
            return false;
        }
    }

    public function addLinkCanonical(string $canonicalUrl): void
    {
        $pageConfig = $this->context->getPageConfig();
        $type       = 'canonical';
        $pageConfig->addRemotePageAsset(
            htmlentities($canonicalUrl),
            $type,
            ['attributes' => ['rel' => $type]]
        );
    }
}
