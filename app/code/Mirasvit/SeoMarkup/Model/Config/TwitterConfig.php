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
use Magento\Store\Model\ScopeInterface as ScopeInterface;

class TwitterConfig
{
    const CARD_TYPE_SMALL_IMAGE = 1;
    const CARD_TYPE_LARGE_IMAGE = 2;
    
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * TwitterConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/twitter/card_type'
        );
    }

    /**
     * @param int $storeId
     *
     * @return mixed
     */
    public function getUsername($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/twitter/username',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
