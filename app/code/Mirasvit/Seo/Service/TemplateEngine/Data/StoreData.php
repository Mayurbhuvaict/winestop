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

namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

class StoreData extends AbstractData
{
    private $storeManager;

    private $scopeConfig;

    private $urlManager;

    private $regionFactory;

    private $countryInformation;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlManager,
        RegionFactory $regionFactory,
        CountryInformationAcquirerInterface $countryInformation
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->urlManager = $urlManager;
        $this->regionFactory = $regionFactory;
        $this->countryInformation = $countryInformation;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('Store Data');
    }

    public function getVariables(): array
    {
        return [
            'name',
            'phone',
            'email',
            'url',
            'street_line_1',
            'street_line_2',
            'city',
            'postcode',
            'store_address',
        ];
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        switch ($attribute) {
            case 'name':
                return $this->getConfigValue('general/store_information/name', $store);

            case 'phone':
                return $this->getConfigValue('general/store_information/phone', $store);

            case 'email':
                return $this->getConfigValue('trans_email/ident_general/email', $store);

            case 'url':
                return $this->urlManager->getBaseUrl();

            case 'street_line_1':
                return $this->getConfigValue('general/store_information/street_line1', $store);

            case 'street_line_2':
                return $this->getConfigValue('general/store_information/street_line2', $store);

            case 'city':
                return $this->getConfigValue('general/store_information/city', $store);

            case 'region_code':
                $regionId = $this->getConfigValue('general/store_information/region_id', $store);
                $region = $this->regionFactory->create();
                $region->load($regionId);
                return (string)$region->getCode();

            case 'postcode':
                return $this->getConfigValue('general/store_information/postcode', $store);

            case 'country_name':
                if ($countryId = $this->getConfigValue('general/store_information/country_id', $store)) {
                    try { // to catch "Requested country is not available." for some stores
                        $country = $this->countryInformation->getCountryInfo($countryId);
                    } catch (\Exception $e) {
                        $country = false;
                    }
                    if ($country) {
                        $countryName = $country->getFullNameLocale();
                        return (string)$countryName;
                    }
                }
                break;

            case 'store_address':
                return $this->getAddress(
                    $this->getValue('street_line_1'),
                    $this->getValue('street_line_2'),
                    $this->getValue('city'),
                    $this->getValue('region_code'),
                    $this->getValue('postcode'),
                    $this->getValue('country_name')
                );
        }

        return $store->getDataUsingMethod($attribute) ?: null;
    }

    private function getAddress(
        string $streetLineOne,
        string $streetLineTwo,
        string $city,
        string $regionCode,
        string $postcode,
        string $countryName
    ): string {
        $storeAddress = '';
        $separator = '<br/>';

        $storeAddressData = [
            'street_line1' => $streetLineOne,
            'street_line2' => $streetLineTwo,
            'city'         => $city,
            'region_code'  => $regionCode,
            'postcode'     => $postcode,
            'country_name' => $countryName,
        ];

        foreach ($storeAddressData as $key => $adress) {
            switch ($key) {
                case 'street_line1':
                case 'street_line2':
                case 'country_name':
                case 'postcode':
                    if ($adress) {
                        $storeAddress .= $adress . $separator;
                    }
                    break;
                case 'city':
                    if ($adress && ($storeAddressData['region_code'] || $storeAddressData['postcode'])) {
                        $storeAddress .= $adress . ', ';
                    } elseif ($adress) {
                        $storeAddress .= $adress . $separator;
                    }
                    break;
                case 'region_code':
                    if ($adress) {
                        $storeAddress .= $adress . ' ';
                    }
            };
        }

        return $storeAddress;
    }

    private function getConfigValue(string $key, StoreInterface $store): ?string
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE, $store) ?: null;
    }
}
