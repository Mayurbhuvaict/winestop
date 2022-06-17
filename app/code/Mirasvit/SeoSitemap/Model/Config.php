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



namespace Mirasvit\SeoSitemap\Model;

use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Context                   $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->context     = $context;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getFrontendSitemapBaseUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_base_url',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getFrontendSitemapMetaTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getFrontendSitemapMetaKeywords($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_keywords',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getFrontendSitemapMetaDescription($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_description',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getFrontendSitemapH1($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_h1',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsShowProducts($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_products',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsShowCategories($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_categories',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsShowNonSalableProducts($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_non_salable_products',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsShowStores($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_stores',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getFrontendLinksLimit($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/links_limit',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getFrontendSitemapColumnCount($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'seositemap/frontend/column_count',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isCapitalLettersEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_capital_letters_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsAddProductImages($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_add_product_images',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsEnableImageFriendlyUrls($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_enable_image_friendly_urls',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getImageUrlTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_url_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getImageSizeWidth($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_size_width',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getImageSizeHeight($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_size_height',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return bool|int
     */
    public function getIsAddProductTags($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_add_product_tags',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getProductTagsChangefreq($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/product_tags_changefreq',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getProductTagsPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/product_tags_priority',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getLinkChangefreq($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/link_changefreq',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getLinkPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/link_priority',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getSplitSize($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/split_size',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     *
     * @return int
     */
    public function getMaxLinks($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/max_links',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function canShowBreadcrumbs()
    {
        return $this->scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE);
    }
}
