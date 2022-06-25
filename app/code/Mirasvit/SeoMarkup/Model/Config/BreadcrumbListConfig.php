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

class BreadcrumbListConfig
{
    const REGISTER_KEY = 'BreadcrumbListRegister';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * BreadcrumbListConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isRsEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/breadcrumb_list/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
