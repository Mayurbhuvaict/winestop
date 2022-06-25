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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Mirasvit\SeoFilter\Model\ConfigProvider;
use Mirasvit\SeoFilter\Model\Context;
use Mirasvit\SeoFilter\Service\Match\Splitting;

class FriendlyUrlService
{
    const QUERY_FILTERS = ['cat'];

    private $rewriteService;

    private $urlService;

    private $splitting;

    private $context;

    private $configProvider;

    /** @var \Magento\Framework\App\Request\Http */
    private $request;
    
    private $urlInstance;
    
    private $urlFinder;
    
    public function __construct(
        RequestInterface $request,
        RewriteService $rewrite,
        UrlService $urlService,
        Splitting $splitting,
        UrlInterface $urlInstance,
        UrlFinderInterface $urlFinder,
        ConfigProvider $configProvider,
        Context $context
    ) {
        $this->request        = $request;
        $this->rewriteService = $rewrite;
        $this->urlService     = $urlService;
        $this->splitting      = $splitting;
        $this->context        = $context;
        $this->configProvider = $configProvider;
        $this->urlInstance    = $urlInstance;
        $this->urlFinder      = $urlFinder;
    }

    public function getUrl(string $attributeCode, string $filterValue, bool $remove = false): string
    {
        $values = explode(ConfigProvider::SEPARATOR_FILTER_VALUES, $filterValue);

        $requiredFilters[$attributeCode] = [];
        if ($attributeCode != '') {
            foreach ($values as $value) {
                $requiredFilters[$attributeCode][$value] = $value;
            }
        }

        // merge with previous filters
        foreach ($this->rewriteService->getActiveFilters() as $attr => $filters) {
            if (!$this->configProvider->isMultiselectEnabled(($attributeCode == 'cat') ? 'category_ids' : $attributeCode) && $attr == $attributeCode) {
                continue;
            }

            foreach ($filters as $filter) {
                if ($filter == $filterValue) {
                    unset($requiredFilters[$attr][$filter]);
                    continue;
                }

                $requiredFilters[$attr][$filter] = $filter;
            }
        }

        // remove filter
        if ($attributeCode != '') {
            if ($remove && isset($requiredFilters[$attributeCode])) {
                foreach ($values as $value) {
                    unset($requiredFilters[$attributeCode][$value]);
                }
            }
        }
        // merge all filters on one line f1-f2-f3-f4
        $filterLines = [];
        $queryParams = [];
        foreach ($requiredFilters as $attrCode => $filters) {
            $attrAlias  = $this->getAttributeRewrite($attrCode);
            $filterLine = [];
            $queryParam = [];

            foreach ($filters as $filter) {
                if (in_array($attrCode, self::QUERY_FILTERS)) {
                    $queryParam[] = $filter;
                } else {
                    $filterLine[] = $this->rewriteService->getOptionRewrite($attrCode, $filter)->getRewrite();
                }
            }

            if (in_array($attrCode, self::QUERY_FILTERS) && count($filters) == 0) {
                $queryParam[] = '';
            }

            if (count($queryParam)) {
                $queryParams[$attrAlias] = implode(',', $queryParam);
            }

            if (count($filterLine)) {
                $filterLines[$attrAlias] = implode(ConfigProvider::SEPARATOR_FILTERS, $filterLine);
            }
        }

        if ($this->configProvider->getUrlFormat() === ConfigProvider::URL_FORMAT_ATTR_OPTIONS) {
            foreach ($filterLines as $attr => $options) {
                $filterLines[$attr] = $attr . '/' . $options;
            }
            ksort($filterLines);

            $filterString = implode('/', $filterLines);
        } else {
            $filterLines = implode(ConfigProvider::SEPARATOR_FILTERS, $filterLines);

            //sort filters
            $values = $this->splitting->splitFiltersString($filterLines);
            asort($values);

            $filterString = implode(ConfigProvider::SEPARATOR_FILTERS, $values);
        }

        //add extra query params
        foreach ($this->urlService->getGetParams() as $param => $value) {
            if (!array_key_exists($param, $requiredFilters)) {
                if ($param === 'p') {
                    continue;
                }

                $queryParams[$param] = $value;
            }
        }

        return $this->getPreparedCurrentUrl($filterString, $queryParams);
    }

    public function getPreparedCurrentUrl(string $filterUrlString, array $queryParams): string
    {
        $suffix = $this->getSuffix();
        $url    = $this->getClearUrl();

        $url = preg_replace('/\?.*/', '', $url);
        $url = ($suffix && $suffix !== '/') ? str_replace($suffix, '', $url) : $url;
        if (!empty($filterUrlString)) {
            if ($separator = $this->configProvider->getPrefix()) {
                $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $separator;
            }

            $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $filterUrlString;
        }

        $url   = $url . $suffix;
        $query = '';
        if (count($queryParams)) {
            $query = '?' . http_build_query($queryParams);
        }

        return $url . $query;
    }

    public function getClearUrl(): string
    {
        $url = '';

        $fullActionName = $this->request->getFullActionName();
        switch ($fullActionName) {
            case 'catalog_category_view':
                $category = $this->context->getCurrentCategory();

                // need this because $category->getUrl() can return not direct URL (rewrite with redirect)
                // we need to be sure that we get the direct category URL
                $rewrite = $this->urlFinder->findOneByData(
                    [
                        'entity_id'     => $category->getId(),
                        'entity_type'   => 'category',
                        'store_id'      => $this->context->getStoreId(),
                        'redirect_type' => 0
                    ]
                );
                
                if ($rewrite) {
                    $category->setData('url', $this->urlInstance->getDirectUrl($rewrite->getRequestPath()));
                }

                $url = $category->getUrl();
                break;

            case 'all_products_page_index_index':
                $url = ObjectManager::getInstance()->get('\Mirasvit\AllProducts\Service\UrlService')->getClearUrl();
                break;

            case 'brand_brand_view':
                $url = ObjectManager::getInstance()->get('Mirasvit\Brand\Service\BrandUrlService')->getBaseBrandUrl();

                $currentUrl  = $this->request->getRequestString();
                $brandConfig = ObjectManager::getInstance()->get('Mirasvit\Brand\Model\Config\GeneralConfig');

                if ($brandConfig->getFormatBrandUrl() == 1) { //short url
                    /** @var \Mirasvit\Brand\Repository\BrandRepository|object $brandRepository */
                    $brandRepository = ObjectManager::getInstance()->get('Mirasvit\Brand\Repository\BrandRepository');

                    foreach ($brandRepository->getFullList() as $brand) {
                        if (preg_match('/\/' . $brand->getUrlKey() . '[\/]*/', $currentUrl)) {
                            $url = str_ireplace($brandConfig->getAllBrandUrl(), $brand->getUrlKey(), $url);
                            break;
                        }
                    }

                } else {
                    $path      = parse_url($currentUrl, PHP_URL_PATH);
                    $pathParts = explode('/', $path);
                    if (isset($pathParts[2])) {
                        $url .= '/' . $pathParts[2]; // pathParts[2] - brand code
                    }
                }

                break;
        }

        return $url;
    }

    public function getSuffix(): string
    {
        $suffix = '';
        if ($this->request->getFullActionName() == 'catalog_category_view') {
            $suffix = $this->urlService->getCategoryUrlSuffix();
        }

        if ($this->request->getFullActionName() == 'brand_brand_view') {
            $suffix = $this->urlService->getBrandUrlSuffix();
        }

        return $suffix;
    }

    public function getAttributeRewrite(string $attributeCode): string
    {
        $attrRewrite = $this->rewriteService->getAttributeRewrite($attributeCode);

        return $attrRewrite ? $attrRewrite->getRewrite() : $attributeCode;
    }
}
