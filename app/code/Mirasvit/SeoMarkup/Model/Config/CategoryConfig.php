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



namespace Mirasvit\SeoMarkup\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CategoryConfig
{
    const PRODUCT_OFFERS_TYPE_DISABLED = 0;
    const PRODUCT_OFFERS_TYPE_CURRENT_PAGE = 1;
    const PRODUCT_OFFERS_TYPE_CURRENT_CATEGORY = 2;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CategoryConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isRemoveNativeRs()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/category/is_remove_native_rs');
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isRsEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isOgEnabled()
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_og_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function getProductOffersType($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/product_offers_type',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCategoryRatingEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/category/is_category_rating_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return int
     */
    public function getDefaultPageSize($store)
    {
        return $this->scopeConfig->getValue(
            'catalog/frontend/grid_per_page',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
