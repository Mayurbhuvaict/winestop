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

class OrganizationConfig
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $socialLinkConfigs = [
                    'seo/seo_markup/organization/youtube_link',
                    'seo/seo_markup/organization/facebook_link',
                    'seo/seo_markup/organization/linkedin_link',
                    'seo/seo_markup/organization/instagram_link',
                    'seo/seo_markup/organization/pinterest_link',
                    'seo/seo_markup/organization/tumblr_link',
                    'seo/seo_markup/organization/twitter_link',
                    ];

    /**
     * OrganizationConfig constructor.
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
            'seo/seo_markup/organization/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomName($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_name',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomName($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_name',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomAddressCountry($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomAddressCountry($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomAddressLocality($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_locality',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomAddressLocality($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/address_locality',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomAddressRegion($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomAddressRegion($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomPostalCode($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomPostalCode($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomStreetAddress($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomStreetAddress($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomTelephone($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function getCustomTelephone($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    public function getCustomFaxNumber($store)
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_fax_number',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomEmail($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/is_custom_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return int
     */
    public function getCustomEmail($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     * @return array
     */
    public function getSocialLinks($store)
    {
        $socialLinks = [];
        foreach ($this->socialLinkConfigs as $socialLinkConfig) {
            $socialLink = $this->scopeConfig->getValue(
                $socialLinkConfig,
                ScopeInterface::SCOPE_STORE,
                $store
            );
            
            if (isset($socialLink)) {
                $socialLinks[] = $socialLink;
            }
        }

        return $socialLinks;
    }
}
