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

class SearchboxConfig
{
    const SEARCH_BOX_TYPE_CATALOG_SEARCH = 1;
    const SEARCH_BOX_TYPE_BLOG_SEARCH = 2;

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getSearchBoxType()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/searchbox/searchbox_type');
    }

    /**
     * @return bool
     */
    public function getBlogSearchUrl()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/searchbox/blog_search_url');
    }
}
