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
 * @package   mirasvit/module-seo-filter
 * @version   1.1.22
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoFilter\Service;

use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlRewrite;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Model\ConfigProvider;
use Mirasvit\SeoFilter\Model\Context;
use Mirasvit\SeoFilter\Repository\RewriteRepository;

class MatchService
{
    const DECIMAL_FILTERS = 'decimalFilters';
    const STATIC_FILTERS  = 'staticFilters';

    private $splitting;

    private $rewriteRepository;

    private $urlRewrite;

    private $urlService;

    private $context;

    private $configProvider;

    private $objectManager;

    private $rewriteService;

    private $moduleManager;

    public function __construct(
        Match\Splitting $splitting,
        RewriteRepository $rewriteRepository,
        RewriteService $rewriteService,
        UrlRewriteCollectionFactory $urlRewrite,
        UrlService $urlService,
        ConfigProvider $configProvider,
        ObjectManagerInterface $objectManager,
        Manager $moduleManager,
        Context $context
    ) {
        $this->splitting         = $splitting;
        $this->rewriteRepository = $rewriteRepository;
        $this->rewriteService    = $rewriteService;
        $this->urlRewrite        = $urlRewrite;
        $this->urlService        = $urlService;
        $this->configProvider    = $configProvider;
        $this->objectManager     = $objectManager;
        $this->moduleManager     = $moduleManager;
        $this->context           = $context;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getParams(): ?array
    {
        if ($this->isNativeRewrite()) {
            return null;
        }

        $categoryId       = 0;
        $isBrandPage      = false;
        $isAllProductPage = false;

        $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();
        $urlPath    = parse_url($currentUrl, PHP_URL_PATH);

        $baseUrlPathAll = 'all';

        if ($this->moduleManager->isEnabled('Mirasvit_AllProducts')) {
            $allProductConfig = $this->objectManager->get('\Mirasvit\AllProducts\Config\Config');

            $baseUrlPathAll = $allProductConfig->isEnabled() ? $allProductConfig->getUrlKey() : $baseUrlPathAll;
        }

        $baseUrlPathBrand    = $this->getBaseBrandUrlPath();
        $baseUrlPathCategory = '';

        if (preg_match('~^/' . $baseUrlPathAll . '/\S+~', $urlPath)) {
            $isAllProductPage = true;
        } elseif (preg_match('~^/' . $baseUrlPathBrand . '/\S+~', $urlPath)) {
            $isBrandPage = true;
        } else {
            $categoryId = $this->getCategoryId();
        }
        if (!$categoryId && !$isBrandPage && !$isAllProductPage) {
            return null;
        }

        if ($categoryId) {
            $baseUrlPathCategory = $this->getCategoryBaseUrlPath($categoryId);
        }

        if ($isBrandPage) {
            $baseUrlPath = $baseUrlPathBrand;
        } elseif ($isAllProductPage) {
            $baseUrlPath = $baseUrlPathAll;
        } else {
            $baseUrlPath = $baseUrlPathCategory;
        }
        $filterData = $baseUrlPath ? $this->splitting->getFiltersString($baseUrlPath) : [];

        $staticFilters  = [];
        $decimalFilters = [];

        $decimalFilters = $this->handleDecimalFilters($filterData, $decimalFilters);

        $staticFilters = $this->handleStockFilters($filterData, $staticFilters);
        $staticFilters = $this->handleRatingFilters($filterData, $staticFilters);
        $staticFilters = $this->handleSaleFilters($filterData, $staticFilters);
        $staticFilters = $this->handleNewFilters($filterData, $staticFilters);
        $staticFilters = $this->handleAttributeFilters($filterData, $staticFilters);

        $params = [];

        foreach ($decimalFilters as $attr => $values) {
            $params[$attr] = implode(ConfigProvider::SEPARATOR_FILTER_VALUES, $values);
        }

        foreach ($staticFilters as $attr => $values) {
            $params[$attr] = implode(ConfigProvider::SEPARATOR_FILTER_VALUES, $values);
        }

        $match = count($filterData) == 0;

        $result = [
            'is_all_pages'  => $isAllProductPage,
            'is_brand_page' => $isBrandPage,
            'category_id'   => $categoryId,
            'params'        => $params,
            'match'         => $match,
        ];

        return $result;
    }

    private function getBaseBrandUrlPath(): string
    {
        $brandPath = 'brand';

        $urlPath = parse_url($this->context->getUrlBuilder()->getCurrentUrl(), PHP_URL_PATH);

        if (!class_exists('Mirasvit\Brand\Model\Config\GeneralConfig')) {
            return $brandPath;
        }

        /** @var \Mirasvit\Brand\Model\Config\GeneralConfig|object $brandConfig */
        $brandConfig = $this->objectManager->get('Mirasvit\Brand\Model\Config\GeneralConfig');

        $brandPath = $brandConfig->getAllBrandUrl();

        /** @var \Mirasvit\Brand\Repository\BrandRepository|object $brandRepository */
        $brandRepository = $this->objectManager->get('Mirasvit\Brand\Repository\BrandRepository');
        foreach ($brandRepository->getList() as $brand) {
            if (preg_match('/\/' . $brand->getUrlKey() . '\/\S+/', $urlPath)) {
                if ($brandConfig->getFormatBrandUrl() == 1) {
                    $brandPath = $brand->getUrlKey();
                    break;
                } else {
                    $brandPath = $brandConfig->getAllBrandUrl() . '/' . $brand->getUrlKey();
                    break;
                }
            }
        }

        return $brandPath;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getCategoryId(): ?int
    {
        $requestPath = trim($this->context->getRequest()->getOriginalPathInfo(), '/');

        if ($categoryId = $this->getCategoryIdByPath($requestPath)) {
            return (int)$categoryId;
        }

        $categoryRewriteCollection = $this->urlRewrite->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->setOrder('request_path', 'DESC');

        $categorySuffix = $this->urlService->getCategoryUrlSuffix();

        $categoryBasePath = '';

        foreach ($categoryRewriteCollection as $categoryRewrite) {
            $path = $this->removeCategorySuffix($categoryRewrite->getRequestPath());

            if (strpos($requestPath, $path . '/') === 0 && strlen($path) > strlen($categoryBasePath)) {
                $categoryBasePath = $path;
                break;
            }
        }

        if (empty($categoryBasePath)) {
            return null;
        }

        $filtersData = $this->splitting->getFiltersString($categoryBasePath);
        $rewrites    = $this->rewriteRepository->getCollection();
        $requestPath = $this->removeCategorySuffix($requestPath);
        $prefix      = $this->configProvider->getPrefix();

        if ($prefix && strripos($requestPath, '/'. $prefix .'/') !== false) {
            $requestPath = str_replace('/'. $prefix .'/', '/' , $requestPath);
        }

        if (isset($filtersData['*'])) {
            $filtersData = $filtersData['*'];
        }

        $filterOptions = [];

        foreach ($filtersData as $attribute => $filter) {
            if ($this->configProvider->getUrlFormat() == ConfigProvider::URL_FORMAT_ATTR_OPTIONS) {
                $requestData = explode('/', $requestPath);
//                $attributeKey = array_search($attribute, $requestData);

//                if (!$attributeKey) {
                    $rewrites = $this->rewriteRepository->getCollection()
                        ->addFieldToFilter(\Mirasvit\SeoFilter\Api\Data\RewriteInterface::STORE_ID, $this->context->getStoreId())
                        ->addFieldToFilter(\Mirasvit\SeoFilter\Api\Data\RewriteInterface::ATTRIBUTE_CODE, $attribute)
                        ->addFieldToFilter(\Mirasvit\SeoFilter\Api\Data\RewriteInterface::OPTION, ['null' => true]);

                    foreach ($rewrites as $rewrite) {
                        $attributeKey = array_search($rewrite->getRewrite(), $requestData);
                        unset($requestData[$attributeKey + 1]);
                        unset($requestData[$attributeKey]);
                    }
//                }

//                unset($requestData[$attributeKey + 1]);
//                unset($requestData[$attributeKey]);
                $requestPath  = implode('/', $requestData);
            } else {
                $filterOptions[] = $filter;
            }
        }

        if (count($filterOptions)) {
            $filterString = implode('-', $filterOptions);

            if (strrpos($requestPath, $filterString) !== false) {
                // substr_replace because category path can include option alias
                $requestPath = substr_replace(
                    $requestPath,
                    '',
                    strrpos($requestPath, $filterString),
                    strlen($filterString)
                );
            }
        }

        $requestPath = trim($requestPath, '/-');
        $requestPath .= $categorySuffix;

        // category rewrites can be with / at the end of the path
        $catId = $this->getCategoryIdByPath($requestPath) ?: $this->getCategoryIdByPath($requestPath . '/');

        return $catId;
    }

    private function removeCategorySuffix(string $path): string
    {
        $categorySuffix = $this->urlService->getCategoryUrlSuffix();

        if (!$categorySuffix) {
            return $path;
        }

        $suffixPosition = strrpos($path, $categorySuffix);

        return $suffixPosition !== false
            ? substr($path, 0, $suffixPosition)
            : $path;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    // private function collectCorrectFilterOptions(array $filter, string $attribute = null): array
    // {
    //     $found = [];

    //     $rewrites = $this->rewriteRepository->getCollection();

    //     $isRange = true;

    //     if ($attribute && !$this->context->isDecimalAttribute($attribute)) {
    //         $isRange = false;
    //     }

    //     foreach ($filter as $value) {
    //         if ($this->isStaticFilterRewrite($value) || ($attribute && $isRange) || is_numeric($value)) {
    //             $found[] = $value;
    //         } else {
    //             foreach ($rewrites as $rewrite) {
    //                 if ($value === $rewrite->getRewrite()) {
    //                     $found[] = $value;
    //                 }
    //             }
    //         }
    //     }

    //     sort($found);

    //     return $found;
    // }

    // private function ensureAttributeRewrite(string $alias): ?string
    // {
    //     $staticFilterLables = [
    //         ConfigProvider::FILTER_RATING,
    //         ConfigProvider::FILTER_NEW,
    //         ConfigProvider::FILTER_SALE,
    //         ConfigProvider::FILTER_STOCK
    //     ];

    //     return $this->rewriteService->getAttributeRewriteByAlias($alias, $this->context->getStoreId()) || in_array($alias, $staticFilterLables)
    //         ? $alias
    //         : null;
    // }

    private function getCategoryIdByPath(string $requestPath): ?int
    {
        $categoryRewrites = $this->urlRewrite
            ->create()
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('entity_type', 'category');

        foreach ($categoryRewrites as $categoryRewrite) {
            if ($categoryRewrite->getRequestPath() == $requestPath) {
                return (int)$categoryRewrite->getEntityId();
            }
        }

        return null;
    }

    // private function isStaticFilterRewrite(string $value): bool
    // {
    //     $staticFilters = [
    //         ConfigProvider::FILTER_SALE,
    //         ConfigProvider::FILTER_NEW,
    //         ConfigProvider::LABEL_RATING_1,
    //         ConfigProvider::LABEL_RATING_2,
    //         ConfigProvider::LABEL_RATING_3,
    //         ConfigProvider::LABEL_RATING_4,
    //         ConfigProvider::LABEL_RATING_5,
    //         ConfigProvider::LABEL_STOCK_IN,
    //         ConfigProvider::LABEL_STOCK_OUT,
    //     ];

    //     return in_array($value, $staticFilters);
    // }

    private function getCategoryBaseUrlPath(int $categoryId): string
    {
        /** @var \Magento\UrlRewrite\Model\UrlRewrite $item */
        $item = $this->urlRewrite->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem();

        $url = (string)$item->getData('request_path');

        return $this->removeCategorySuffix($url);
    }

    private function isNativeRewrite(): bool
    {
        $requestString = trim($this->context->getRequest()->getPathInfo(), '/');

        $requestPathRewrite = $this->urlRewrite->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('request_path', $requestString);

        return $requestPathRewrite->getSize() > 0;
    }

    private function handleStockFilters(array &$filterData, array $staticFilters): array
    {
        $options = [
            1 => ConfigProvider::LABEL_STOCK_OUT,
            2 => ConfigProvider::LABEL_STOCK_IN,
        ];

        return $this->processBuiltInFilters(ConfigProvider::FILTER_STOCK, $options, $filterData, $staticFilters);
    }

    private function handleRatingFilters(array &$filterData, array $staticFilters): array
    {
        $options = [
            1 => ConfigProvider::LABEL_RATING_1,
            2 => ConfigProvider::LABEL_RATING_2,
            3 => ConfigProvider::LABEL_RATING_3,
            4 => ConfigProvider::LABEL_RATING_4,
            5 => ConfigProvider::LABEL_RATING_5,
        ];

        return $this->processBuiltInFilters(ConfigProvider::FILTER_RATING, $options, $filterData, $staticFilters);
    }

    private function handleSaleFilters(array &$filterData, array $staticFilters): array
    {
        $options = [
            1 => ConfigProvider::FILTER_SALE,
        ];

        return $this->processBuiltInFilters(ConfigProvider::FILTER_SALE, $options, $filterData, $staticFilters);
    }

    private function handleNewFilters(array &$filterData, array $staticFilters): array
    {
        $options = [
            1 => ConfigProvider::FILTER_NEW,
        ];

        return $this->processBuiltInFilters(ConfigProvider::FILTER_NEW, $options, $filterData, $staticFilters);
    }

    private function handleAttributeFilters(array &$filterData, array $staticFilters): array
    {
        foreach ($filterData as $attrCode => $filterValues) {
            $rewriteCollection = $this->rewriteRepository->getCollection()
                ->addFieldToFilter(RewriteInterface::REWRITE, ['in' => $filterValues])
                ->addFieldToFilter(RewriteInterface::STORE_ID, $this->context->getStoreId());

            if ($attrCode != '*') {
                $rewriteCollection->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE, $attrCode);
            }

            if ($rewriteCollection->getSize() == count($filterValues)) {
                /** @var RewriteInterface $rewrite */
                foreach ($rewriteCollection as $rewrite) {
                    $rewriteAttributeCode = $rewrite->getAttributeCode();
                    $optionId             = $rewrite->getOption();

                    $staticFilters[$rewriteAttributeCode][] = $optionId;
                }

                unset($filterData[$attrCode]);
            } else {
                $rewriteCollection = $this->rewriteRepository->getCollection()
                    ->addFieldToFilter(RewriteInterface::ATTRIBUTE_CODE, $attrCode)
                    ->addFieldToFilter(RewriteInterface::STORE_ID, $this->context->getStoreId());

                $rewrites = [];
                foreach ($rewriteCollection as $rewrite) {
                    $rewrites[$rewrite->getOption()] = $rewrite->getRewrite();
                }
                $filterString = implode('-', $filterValues);

                foreach ($rewrites as $optionId => $rew) {
                    $str = str_replace($rew, '', $filterString);
                    if ($filterString != $str) {
                        $filterString = $str;

                        $staticFilters[$attrCode][] = $optionId;
                    }
                }
                unset($filterData[$attrCode]);
            }
        }

        return $staticFilters;
    }

    private function handleDecimalFilters(array &$filterData, array $decimalFilters): array
    {
        foreach ($filterData as $attrCode => $filterValues) {
            if ($attrCode != '*') {
                if ($this->context->isDecimalAttribute($attrCode)) {
                    $option = implode(ConfigProvider::SEPARATOR_FILTERS, $filterValues);

                    $decimalFilters[$attrCode][] = $option;

                    unset($filterData[$attrCode]);
                }
            } else {
                foreach ($filterValues as $key => $filterValue) {
                    if (strpos($filterValue, ConfigProvider::SEPARATOR_DECIMAL) !== false) {
                        $exploded = explode(ConfigProvider::SEPARATOR_DECIMAL, $filterValue);
                        $attrCode = $exploded[0];
                        unset($exploded[0]);

                        $option = implode(ConfigProvider::SEPARATOR_FILTERS, $exploded);
                        $decimalFilters[$attrCode][] = $option;

                        unset($filterData['*'][$key]);
                    }
                }
            }
        }

        return $decimalFilters;
    }

    private function processBuiltInFilters(string $attrCode, array $options, array &$filterData, array $staticFilters): array
    {
        foreach ($options as $key => $label) {
            foreach ($filterData as $fKey => $value) {
                if (in_array($label, $value)) {
                    $staticFilters[$attrCode][] = $key;

                    $vKey = array_search($label, $filterData[$fKey]);
                    unset($filterData[$fKey][$vKey]);
                }
            }
        }

        return $staticFilters;
    }
}
