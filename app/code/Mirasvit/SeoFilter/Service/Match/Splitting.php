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



namespace Mirasvit\SeoFilter\Service\Match;

use Mirasvit\SeoFilter\Model\ConfigProvider;
use Mirasvit\SeoFilter\Model\Context;
use Mirasvit\SeoFilter\Service\RewriteService;
use Mirasvit\SeoFilter\Service\UrlService;

class Splitting
{
    private $rewriteService;

    private $urlService;

    private $configProvider;

    private $context;

    public function __construct(
        RewriteService $rewriteService,
        UrlService $urlService,
        ConfigProvider $configProvider,
        Context $context
    ) {
        $this->rewriteService = $rewriteService;
        $this->urlService     = $urlService;
        $this->configProvider = $configProvider;
        $this->context        = $context;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string $basePath
     *
     * @return array
     */
    public function getFiltersString(string $basePath): array
    {
        $uri = trim($this->context->getRequest()->getOriginalPathInfo(), '/');

        $suffix = $this->urlService->getCategoryUrlSuffix();
        if ($suffix && substr($uri, -strlen($suffix)) === $suffix) {
            $uri = substr($uri, 0, -strlen($suffix));
        }

//        $filtersString = trim(str_replace($basePath, '', $uri), '/');

        $filtersString = '';

        if (strpos($uri, $basePath) === 0) {
            $filtersString = trim(substr($uri, strlen($basePath)), '/');
        }


        $prefix = $this->configProvider->getPrefix();
        if ($prefix && substr($filtersString, 0, strlen($prefix)) === $prefix) {
            $filtersString = trim(substr($filtersString, strlen($prefix)), '/');
        }

        if ($this->configProvider->getUrlFormat() == ConfigProvider::URL_FORMAT_ATTR_OPTIONS) {
            $result     = [];
            $filterInfo = explode('/', $filtersString);

            for ($i = 0; $i <= count($filterInfo) - 2; $i += 2) {
                $attributeAlias = (string)$filterInfo[$i];
                $rewrite        = $this->rewriteService->getAttributeRewriteByAlias($attributeAlias);
                $attributeCode  = $rewrite ? $rewrite->getAttributeCode() : $attributeAlias;
                foreach ($this->splitFiltersString($filterInfo[$i + 1]) as $opt) {
                    $result[$attributeCode][] = $opt;
                }
            }
        } else {
            $result     = [];
            $filterInfo = explode('/', $filtersString);

            foreach ($filterInfo as $part) {
                foreach ($this->splitFiltersString($part) as $opt) {
                    $result['*'][] = $opt;
                }
            }
        }

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function splitFiltersString(string $filtersString): array
    {
        $staticFilters = [
            ConfigProvider::FILTER_SALE,
            ConfigProvider::FILTER_NEW,
            ConfigProvider::LABEL_RATING_1,
            ConfigProvider::LABEL_RATING_2,
            ConfigProvider::LABEL_RATING_3,
            ConfigProvider::LABEL_RATING_4,
            ConfigProvider::LABEL_RATING_5,
            ConfigProvider::LABEL_STOCK_IN,
            ConfigProvider::LABEL_STOCK_OUT,
        ];

        $filterInfo = explode(ConfigProvider::SEPARATOR_FILTERS, $filtersString);
        $alias = '';
        $result = [];

        for ($i = 0; $i < count($filterInfo);) {
            $alias = $alias !== '' ? $alias . ConfigProvider::SEPARATOR_FILTERS . $filterInfo[$i] : $filterInfo[$i];

            if (
                in_array($alias, $staticFilters)
                || preg_match('#\w+:\d+(\.\d*)?:\d+(\.\d*)?#is', $alias)
                || preg_match('#\d+(\.\d*)?\-\d+(\.\d*)?#', $alias)
            ) {
                $result[] = $alias;
                $alias = '';
                $i++;

                continue;
            }

            // search for smallest alias
            if (!$this->rewriteService->getOptionRewriteByAlias($alias)) {
                $i++;

                continue;
            }

            // search for longest alias (preferable)
            for ($j = $i+1; $j < count($filterInfo); $j++) {
                $attempt = $alias . ConfigProvider::SEPARATOR_FILTERS . $filterInfo[$j];
                if ($this->rewriteService->getOptionRewriteByAlias($attempt)) {
                    $alias = $attempt;
                } else {
                    break;
                }
            }

            if ($this->rewriteService->getOptionRewriteByAlias($alias)) {
                $result[] = $alias;
                $alias = '';
            }

            $i = $j;
        }

        return $result;
    }
}
