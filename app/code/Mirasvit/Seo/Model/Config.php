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

namespace Mirasvit\Seo\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Seo\Model\Cookie\Cookie;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Config
{
    const TRAILING_SLASH_DISABLE = 0;
    const NO_TRAILING_SLASH      = 1;
    const TRAILING_SLASH         = 2;

    const URL_FORMAT_SHORT = 1;
    const URL_FORMAT_LONG  = 2;

    const NOINDEX_NOFOLLOW = 1;
    const NOINDEX_FOLLOW   = 2;
    const INDEX_NOFOLLOW   = 3;
    const INDEX_FOLLOW     = 4;

    const PRODUCTS_WITH_REVIEWS_NUMBER = 1;
    const REVIEWS_NUMBER               = 2;
    const OPENGRAPH_LOGO_IMAGE         = 1;
    const OPENGRAPH_PRODUCT_IMAGE      = 2;
    const INFO_IP                      = 1;
    const INFO_COOKIE                  = 2;
    const COOKIE_DEL_BUTTON            = 'Delete cookie';


    //seo template rule

    // open graph

    const COOKIE_ADD_BUTTON = 'Add cookie';
    const BYPASS_COOKIE     = 'info_bypass_cookie';

    //seo info

    private $scopeConfig;

    private $cookie;

    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Cookie $cookie,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->cookie       = $cookie;
        $this->storeManager = $storeManager;
    }

    public function isAddCanonicalUrl(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_add_canonical_url');
    }

    public function isAddLongestCanonicalProductUrl(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_longest_canonical_url');
    }

    public function getAssociatedCanonicalConfigurableProduct(): int
    {
        return (int)$this->scopeConfig->getValue('seo/general/associated_canonical_configurable_product');
    }

    public function getAssociatedCanonicalGroupedProduct(): int
    {
        return (int)$this->scopeConfig->getValue('seo/general/associated_canonical_grouped_product');
    }

    public function getAssociatedCanonicalBundleProduct(): int
    {
        return (int)$this->scopeConfig->getValue('seo/general/associated_canonical_bundle_product');
    }

    public function getCanonicalStoreWithoutStoreCode(int $store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/general/canonical_store_without_store_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getCrossDomainStore(int $store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/general/crossdomain',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isPreferCrossDomainHttps(int $store = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/general/crossdomain_prefer_https',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isPaginatedCanonical(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/paginated_canonical');
    }

    public function getCanonicalUrlIgnorePages(): array
    {
        $pages = (string)$this->scopeConfig->getValue('seo/general/canonical_url_ignore_pages');
        $pages = explode("\n", trim($pages));
        $pages = array_map('trim', $pages);

        return $pages;
    }

    public function getNoindexPages(int $store = null): array
    {
        if (empty($store)) {
            $store = $this->storeManager->getStore()->getId();
        }

        $storePages = $this->getOptionData(
            (string)$this->scopeConfig->getValue(
                'seo/general/noindex_pages2',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
        );

        $generalPages = $this->getOptionData(
            (string)$this->scopeConfig->getValue('seo/general/noindex_pages2')
        );

        $pages = array_merge($storePages, $generalPages);

        $result = [];

        foreach ($pages as $value) {
            if (!is_array($value)) {
                continue;
            }

            $result[] = new \Magento\Framework\DataObject($value);
        }

        return $result;
    }

    public function getHttpsNoindexPages(): int
    {
        return (int)$this->scopeConfig->getValue('seo/general/https_noindex_pages');
    }

    public function isPagingPrevNextEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_paging_prevnext');
    }

    public function isCategoryMetaTagsUsed(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_category_meta_tags_used');
    }

    public function isProductMetaTagsUsed(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_product_meta_tags_used');
    }

    public function isUseHtmlSymbolsInMetaTags(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_use_html_symbols_in_meta_tags');
    }

    public function isUseShortDescriptionForCategories(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_use_short_description_for_categories');
    }

    public function getMetaDescriptionPageNumber(int $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/extended/meta_description_page_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMetaTitleMaxLength(int $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/extended/meta_title_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMetaDescriptionMaxLength(int $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/extended/meta_description_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getProductNameMaxLength(int $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/extended/product_name_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getProductShortDescriptionMaxLength(int $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/extended/product_short_description_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isRedirectToLowercaseEnabled(int $store): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/extended/redirect_to_lowercase',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getAllowedLowercasePageTypes(int $store): array
    {
        $data = (array)SerializeService::decode($this->scopeConfig->getValue(
            'seo/extended/to_lowercase_allowed_types',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));

        $result = [];
        foreach ($data as $item) {
            if (isset($item['expression'])) {
                $result[] = $item['expression'];
            }
        }

        return $result;
    }

    /**
     * SEO URL
     */
    public function isEnabledSeoUrls(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/url/layered_navigation_friendly_urls');
    }

    public function getTrailingSlash(): int
    {
        return (int)$this->scopeConfig->getValue('seo/url/trailing_slash');
    }

    public function getProductUrlFormat(): int
    {
        return (int)$this->scopeConfig->getValue('seo/url/product_url_format');
    }

    public function getProductUrlKey(int $store): string
    {
        return (string)$this->scopeConfig->getValue(
            'seo/url/product_url_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isEnabledRemoveParentCategoryPath(int $storeId = null, int $websiteId = null): bool
    {
        if ($websiteId) {
            return (bool)$this->scopeConfig->getValue(
                'seo/url/use_category_short_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
        }

        return (bool)$this->scopeConfig->getValue(
            'seo/url/use_category_short_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * INFO
     */
    public function isInfoEnabled(int $storeId = null): bool
    {
        if (!$this->_isInfoAllowed()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            'seo/info/info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isShowAltLinkInfo(int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/info/alt_link_info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isShowTemplatesRewriteInfo(int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/info/templates_rewrite_info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if "Use Categories Path for Product URLs" enabled
     */
    public function isProductLongUrlEnabled(int $storeId): bool
    {
        return (bool)$this->scopeConfig->getValue(
            \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAddStoreCodeToUrlsEnabled(): string
    {
        return (string)$this->scopeConfig->getValue(\Magento\Store\Model\Store::XML_PATH_STORE_IN_URL);
    }

    public function isApplyUrlKeyForNewProducts(int $storeId): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/url/apply_url_key_for_new_products',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    protected function _isInfoAllowed(int $storeId = null): bool
    {
        $info = $this->scopeConfig->getValue(
            'seo/info/info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (($info == self::INFO_COOKIE)
            && $this->cookie->isCookieExist()) {
            return true;
        } elseif ($info == self::INFO_IP) {
            $ips = (string)$this->scopeConfig->getValue(
                'seo/info/allowed_ip',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            if ($ips == '') {
                return true;
            }
            if (!isset($_SERVER['REMOTE_ADDR'])) {
                return false;
            }
            $ips = explode(',', $ips);
            $ips = array_map('trim', $ips);

            return in_array($_SERVER['REMOTE_ADDR'], $ips);
        }

        return false;
    }

    private function getOptionData(string $data): array
    {
        $result = [];

        if ($decode = json_decode($data, true)) {
            $result = $decode;
        } else {
            $result = SerializeService::decode($data);
            if (!$result) {
                $result = [0 => $data];
            }
        }

        return $result;
    }
}
