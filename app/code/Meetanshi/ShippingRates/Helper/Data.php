<?php

namespace Meetanshi\ShippingRates\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollection;
use Magento\Eav\Model\Config;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $countryCollection;
    protected $regionCollection;
    protected $customergroupCollection;
    protected $eavConfig;

    public function __construct(Context $context, CustomerGroupCollection $customergroupCollection, CountryCollection $countryCollection, RegionCollection $regionCollection, Config $eavConfig)
    {
        parent::__construct($context);
        $this->customergroupCollection = $customergroupCollection;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        $this->eavConfig = $eavConfig;
    }

    public function getAllGroups()
    {
        $customerGroups = $this->customergroupCollection->create()->load()->toOptionArray();
        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, ['value' => 0, 'label' => __('NOT LOGGED IN')]);
        }
        return $customerGroups;
    }

    public function getStatuses()
    {
        return [
            '0' => __('Inactive'),
            '1' => __('Active'),
        ];
    }

    public function getStates()
    {
        $data = [];
        $countries = $this->getCountries();
        $collection = $this->regionCollection->create()->getData();
        foreach ($collection as $state) {
            if(isset($state['country_id'])){
                $data[$state['region_id']] = $countries[$state['country_id']] . "/" . $state['default_name'];
            }
        }
        asort($data);
        $hashAll['0'] = 'All';
        $data = $hashAll + $data;
        return $data;
    }

    public function getCountries()
    {
        $data = [];
        $countries = $this->countryCollection->create()->toOptionArray();
        foreach ($countries as $country) {
            if ($country['value']) {
                $data[$country['value']] = $country['label'];
            }
        }
        asort($data);
        $dataAll['0'] = 'All';
        $data = $dataAll + $data;
        return $data;
    }

    public function getShippingType()
    {
        $data = [];
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'shipping_type');
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        foreach ($options as $option) {
            $data[$option['value']] = $option['label'];
        }
        asort($data);
        $dataAll['0'] = 'All';
        $data = $dataAll + $data;
        return $data;
    }

    public function allowPromo()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/allow_free_shipping', ScopeInterface::SCOPE_STORE);
    }

    public function ignoreVirtual()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/ignore_virtual', ScopeInterface::SCOPE_STORE);
    }

    public function dontSplit()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/dont_split', ScopeInterface::SCOPE_STORE);
    }

    public function getAfterDiscount()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/after_discount', ScopeInterface::SCOPE_STORE);
    }

    public function getIncludingTax()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/including_tax', ScopeInterface::SCOPE_STORE);
    }

    public function allowNumericZip()
    {
        return $this->scopeConfig->getValue('carriers/shippingrates/allow_numeric_zip', ScopeInterface::SCOPE_STORE);
    }

    public function getNumericZip($data)
    {
        $array = ['area' => '', 'district' => ''];

        if (!empty($data)) {
            $zipSpell = str_split($data);
            foreach ($zipSpell as $element) {
                if ($element === ' ') {
                    break;
                }
                if (is_numeric($element)) {
                    $array['district'] = $array['district'] . $element;
                } elseif (empty($array['district'])) {
                    $array['area'] = $array['area'] . $element;
                }
            }
        }

        return $array;
    }
}
