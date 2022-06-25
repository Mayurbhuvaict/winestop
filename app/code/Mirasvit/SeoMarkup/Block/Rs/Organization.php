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



namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Locale\ListsInterface as LocaleListsInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Theme\Block\Html\Header\Logo;
use Mirasvit\SeoMarkup\Model\Config\OrganizationConfig;

class Organization extends Template
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var OrganizationConfig
     */
    private $organizationConfig;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var LocaleListsInterface
     */
    private $localeLists;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var Logo
     */
    private $logo;

    /**
     * Organization constructor.
     * @param OrganizationConfig $organizationConfig
     * @param LocaleListsInterface $localeLists
     * @param RegionFactory $regionFactory
     * @param Logo $logo
     * @param Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        OrganizationConfig $organizationConfig,
        LocaleListsInterface $localeLists,
        RegionFactory $regionFactory,
        Logo $logo,
        Context $context
    ) {
        $this->organizationConfig = $organizationConfig;
        $this->localeLists        = $localeLists;
        $this->regionFactory      = $regionFactory;
        $this->logo               = $logo;
        $this->context            = $context;

        $this->store = $context->getStoreManager()->getStore();

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (
            !$this->organizationConfig->isRsEnabled($this->store) 
            || $this->getRequest()->getFullActionName() !== 'cms_index_index'
        ) {
            return false;
        }

        $data = $this->getJsonData();

        return '<script type="application/ld+json">' . \Zend_Json::encode($data) . '</script>';
    }

    /**
     * @return array
     */
    private function getJsonData()
    {
        $data = [
            "@context" => "http://schema.org",
            "@type"    => "Organization",
        ];

        $values = [
            'url'       => $this->getBaseUrl(),
            'logo'      => $this->getLogoUrl(),
            'name'      => $this->getName(),
            'telephone' => $this->getTelephone(),
            'faxNumber' => $this->getFaxNumber(),
            'email'     => $this->getEmail()
        ];

        foreach ($values as $key => $value) {
            $value = trim($value);

            if ($value) {
                $data[$key] = $value;
            }
        }

        $values  = [
            'addressCountry'  => $this->getAddressCountry(),
            'addressLocality' => $this->getAddressLocality(),
            'postalCode'      => $this->getPostalCode(),
            'streetAddress'   => $this->getStreetAddress(),
            'addressRegion'   => $this->getAddressRegion(),
        ];
        $address = [];

        foreach ($values as $key => $value) {
            $value = trim($value);
            if ($value) {
                $address[$key] = $value;
            }
        }

        if (count($address)) {
            $data['address'] = array_merge([
                '@type' => 'PostalAddress',
            ], $address);
        }

        if ($socialLinks = $this->getSocialLinks()) {
            $data['sameAs'] = $socialLinks;
        }

        return $data;
    }

    /**
     * @return string
     */
    private function getName()
    {
        if ($this->organizationConfig->isCustomName($this->store)) {
            return $this->organizationConfig->getCustomName($this->store);
        }

        return $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_NAME);
    }

    /**
     * @return string
     */
    private function getTelephone()
    {
        if ($this->organizationConfig->isCustomTelephone($this->store)) {
            return $this->organizationConfig->getCustomTelephone($this->store);
        }

        return $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_PHONE);
    }

    /**
     * @return string
     */
    private function getFaxNumber()
    {
        return $this->organizationConfig->getCustomFaxNumber($this->store);
    }

    /**
     * @return string
     */
    private function getEmail()
    {
        if ($this->organizationConfig->isCustomEmail($this->store)) {
            return $this->organizationConfig->getCustomEmail($this->store);
        }

        return $this->context->getScopeConfig()->getValue('trans_email/ident_general/email');
    }

    /**
     * @return string
     */
    public function getAddressCountry()
    {
        if ($this->organizationConfig->isCustomAddressCountry($this->store)) {
            return $this->organizationConfig->getCustomAddressCountry($this->store);
        }

        return $this->localeLists->getCountryTranslation(
            $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_COUNTRY_CODE)
        );
    }

    /**
     * @return string
     */
    public function getAddressLocality()
    {
        if ($this->organizationConfig->isCustomAddressLocality($this->store)) {
            return $this->organizationConfig->getCustomAddressLocality($this->store);
        }

        return $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_CITY);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        if ($this->organizationConfig->isCustomPostalCode($this->store)) {
            return $this->organizationConfig->getCustomPostalCode($this->store);
        }

        return $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_POSTCODE);
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        if ($this->organizationConfig->isCustomStreetAddress($this->store)) {
            return $this->organizationConfig->getCustomStreetAddress($this->store);
        }

        return $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE1)
            . ' '
            . $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE2);
    }

    /**
     * @return string
     */
    public function getAddressRegion()
    {
        if ($this->organizationConfig->isCustomAddressRegion($this->store)) {
            return $this->organizationConfig->getCustomAddressRegion($this->store);
        }

        $regionId = $this->store->getConfig(StoreInformation::XML_PATH_STORE_INFO_REGION_CODE);

        return $this->regionFactory->create()->load($regionId)->getCode();
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->context->getUrlBuilder()->getBaseUrl();
    }

    /**
     * @return array
     */
    public function getSocialLinks()
    {
        return $this->organizationConfig->getSocialLinks($this->store);
    }
}
