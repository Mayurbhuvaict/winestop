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


namespace Mirasvit\Seo\Plugin\Adminhtml;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Model\Config\ProductUrlTemplateConfig;
use Mirasvit\Seo\Service\TemplateEngineService;
use Mirasvit\Seo\Service\UrlTemplate\ProductUrlTemplateService;

/** @see \Magento\CatalogUrlRewrite\Model\ProductScopeRewriteGenerator */
class ProductUrlKeyPlugin
{
    private $templateEngineService;

    private $productUrlTemplateConfig;

    private $productRepository;

    private $searchBuilder;

    private $storeManager;

    private $productUrlTemplateService;

    private $seoConfig;

    public function __construct(
        TemplateEngineService $templateEngineService,
        ProductUrlTemplateConfig $productUrlTemplateConfig,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchBuilder,
        StoreManagerInterface $storeManager,
        ProductUrlTemplateService $productUrlTemplateService,
        Config $seoConfig
    ) {
        $this->templateEngineService     = $templateEngineService;
        $this->productUrlTemplateConfig  = $productUrlTemplateConfig;
        $this->productRepository         = $productRepository;
        $this->searchBuilder             = $searchBuilder;
        $this->storeManager              = $storeManager;
        $this->productUrlTemplateService = $productUrlTemplateService;
        $this->seoConfig                 = $seoConfig;
    }

    /**
     * @param \Magento\CatalogUrlRewrite\Model\ProductScopeRewriteGenerator\Interceptor $subject
     * @param array $result
     * @param \Magento\Framework\Data\Collection|\Magento\Catalog\Model\Category[] $productCategories
     * @param Product $product
     *
     * @return array
     */
    public function afterGenerateForGlobalScope($subject, array $result, $productCategories, Product $product) :array
    {
        if (!$product->isObjectNew()) {
            return $result;
        }

        /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $rewrite */
        foreach ($result as $key => $rewrite) {
            $requestPath = $this->updateRequestPath(
                $product,
                (int)$rewrite->getStoreId(),
                (string)$rewrite->getRequestPath()
            );

            $rewrite->setRequestPath($requestPath);

            $result[$key] = $rewrite;
        }

        $this->updateUrlKey($product, 0);

        return $result;
    }

    private function updateRequestPath(Product $product, int $storeId, string $requestPath) :string
    {
        if (strpos($requestPath, $product->getUrlKey()) === false) {
            return $requestPath;
        }

        $urlKey = $this->updateUrlKey($product, $storeId);

        return str_replace($product->getUrlKey(), $urlKey, $requestPath);
    }

    private function updateUrlKey(Product $product, int $storeId) :string
    {
        $urlKeyTemplate = $this->productUrlTemplateConfig->getProductUrlKey($storeId);

        if (
            !$urlKeyTemplate
            || !$this->seoConfig->isApplyUrlKeyForNewProducts($storeId)
            || $urlKeyTemplate == '[product_name]'
        ) {
            return $product->getUrlKey();
        }

        $store = $this->storeManager->getStore($storeId);

        $urlKey = $this->templateEngineService->render(
            $urlKeyTemplate,
            [
                'product' => $product,
                'store'   => $store,
            ]
        );

        $urlKey = $product->formatUrlKey($urlKey);

        //search for unique urlKey
        for ($i = 1; $i < 100; $i++) {
            $searchCriteria = $this->searchBuilder
                ->addFilter("url_key", $urlKey)
                ->addFilter("store", $store)
                ->setPageSize(1)
                ->create();

            $products = $this->productRepository->getList($searchCriteria);

            if ($products->getTotalCount()) {
                $revUrlKey = strrev($urlKey);
                $revNum    = (int)$revUrlKey;
                $number    = (int)strrev($revNum); //0 or some int
                $suffix    = "-".$number;
                $pos       = strpos($urlKey, $suffix);

                if ($pos !== false && $pos == strlen($urlKey) - strlen($suffix)) {
                    $urlKey = substr($urlKey, 0, $pos);//url_key without suffix
                }

                $number++;
                $urlKey .= "-".$number;

                continue;
            }

            break;
        }

        $nProduct = clone $product;

        $nProduct->setStoreId($storeId);

        $this->productUrlTemplateService->updateEntityUrlKey($urlKey, $nProduct);

        return $urlKey;
    }
}
