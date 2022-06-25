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

namespace Mirasvit\SeoContent\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const PAGE_NUMBER_POSITION_AT_BEGIN            = 1;
    const PAGE_NUMBER_POSITION_AT_END              = 2;
    const PAGE_NUMBER_POSITION_AT_BEGIN_WITH_FIRST = 3;
    const PAGE_NUMBER_POSITION_AT_END_WITH_FIRST   = 4;

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function useProductMetaTags(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/seo_content/meta/is_product_meta_tags_used',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function useCategoryMetaTags(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/seo_content/meta/is_category_meta_tags_used',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function useCmsMetaTags(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/seo_content/meta/is_cms_meta_tags_used',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function addPrefixSuffixToMetaTitle(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'seo/seo_content/meta/is_add_prefix_suffix',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaTitlePageNumber(StoreInterface $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/seo_content/pagination/meta_title_page_number',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMetaDescriptionPageNumber(StoreInterface $store): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/seo_content/pagination/meta_description_page_number',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMetaTitleLength(StoreInterface $store): int
    {
        $value = (int)$this->scopeConfig->getValue(
            'seo/seo_content/limiter/meta_title_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value && $value < 25 ? 55 : $value;
    }

    public function getMetaDescriptionLength(StoreInterface $store): int
    {
        $value = (int)$this->scopeConfig->getValue(
            'seo/seo_content/limiter/meta_description_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value && $value < 25 ? 150 : $value;
    }

    public function getProductNameLength(StoreInterface $store): int
    {
        $value = (int)$this->scopeConfig->getValue(
            'seo/seo_content/limiter/product_name_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value && $value < 10 ? 25 : $value;
    }

    public function getProductShortDescriptionLength(StoreInterface $store): int
    {
        $value = (int)$this->scopeConfig->getValue(
            'seo/seo_content/limiter/product_short_description_max_length',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value && $value < 25 ? 90 : $value;
    }
}
