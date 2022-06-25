<?php

namespace Magecomp\Extrafee\Model;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Reflection\DataObjectProcessor;

class ShippingMethodManagement extends \Magento\Quote\Model\ShippingMethodManagement
{
    private $dataProcessor;
    /**
     * Get estimated rates
     *
     * @param Quote $quote
     * @param int $country
     * @param string $postcode
     * @param int $regionId
     * @param string $region
     * @param \Magento\Framework\Api\ExtensibleDataInterface|null $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @deprecated 100.1.6
     */
    protected function getEstimatedRates(
        \Magento\Quote\Model\Quote $quote,
        $country,
        $postcode,
        $regionId,
        $region,
        $address = null
    ) {
        if (!$address) {
            $address = $this->getAddressFactory()->create()
                ->setCountryId($country)
                ->setPostcode($postcode)
                ->setRegionId($regionId)
                ->setRegion($region);
        }
        return $this->getShippingMethods($quote, $address);
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddress($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        return $this->getShippingMethods($quote, $address);
    }

    /**
     * @inheritdoc
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        return $this->getShippingMethods($quote, $address);
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddressId($cartId, $addressId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->addressRepository->getById($addressId);

        return $this->getShippingMethods($quote, $address);
    }

    private function getShippingMethods(Quote $quote, $address)
    {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $addressData = $this->extractAddressData($address);
        if (array_key_exists('extension_attributes', $addressData)) {
            unset($addressData['extension_attributes']);
        }
        $shippingAddress->addData($addressData);
        $shippingAddress->setCollectShippingRates(true);

        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        $rates=[];
        foreach ($shippingRates as $k => $carrierRates) {
            if ($quote->getDeliveryMethod() == 'ship') {
                if($k == 'storepickup' || $k == 'localshipping'){
                    continue;
                }
            } else if ($quote->getDeliveryMethod() == 'local') {
                if($k != 'localshipping'){
                    continue;
                }
            } else {
                if($k != 'storepickup'){
                    continue;
                }
            }
            if ($k == 'shippingrates') {
                foreach ($carrierRates as $rate) {
                    $rates[$rate->getCarrierTitle()][]=$rate;
                }
                $i=1;
                foreach ($rates as $_carrier) {
                    foreach($_carrier as $_r){
                        if ($i==1) {
                            $this->updateShippingMethod($shippingAddress, $_r);
                        }
                        $output[] = $this->converter->modelToDataObject($_r, $quote->getQuoteCurrencyCode());    
                        $i++;
                    }
                }
            } else {
                $i=1;
                foreach ($carrierRates as $rate) {
                    if ($i==1) {
                        $this->updateShippingMethod($shippingAddress, $rate);
                    }
                    $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
                    $i++;
                }
            }
        }
        return $output;
    }

    private function updateShippingMethod($shippingAddress, $rate) {
        $shippingAddress->setShippingMethod($rate->getCode())->save();
    }

    /**
     * Get transform address interface into Array
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface  $address
     * @return array
     */
    private function extractAddressData($address)
    {
        $className = \Magento\Customer\Api\Data\AddressInterface::class;
        if ($address instanceof \Magento\Quote\Api\Data\AddressInterface) {
            $className = \Magento\Quote\Api\Data\AddressInterface::class;
        } elseif ($address instanceof EstimateAddressInterface) {
            $className = EstimateAddressInterface::class;
        }
        return $this->getDataObjectProcessor()->buildOutputDataArray(
            $address,
            $className
        );
    }

    private function extractAddressExtensionData($addressExtension)
    {
        $className = \Magento\Quote\Api\Data\AddressExtensionInterface::class;
        return $this->getDataObjectProcessor()->buildOutputDataArray(
            $addressExtension,
            $className
        );
    }

    /**
     * Gets the data object processor
     *
     * @return \Magento\Framework\Reflection\DataObjectProcessor
     * @deprecated 101.0.0
     */
    private function getDataObjectProcessor()
    {
        if ($this->dataProcessor === null) {
            $this->dataProcessor = ObjectManager::getInstance()
                ->get(DataObjectProcessor::class);
        }
        return $this->dataProcessor;
    }
}
