<?php

namespace Magecomp\Extrafee\Plugin;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Quote\Model\ShippingAddressManagement;
use Magento\Quote\Model\ShippingMethodManagement;
use Magento\Framework\App\ObjectManager;

class DefaultConfigModifier
{
    protected $checkoutSession;
    protected $quoteFactory;
    protected $quoteAddressFactory;
    protected $shippingAddressManagement;
    protected $shippingMethodManagement;
    protected $allowedCity = ['burlingame', 'hillsborough', 'sanmateo', 'millbrae', 'belmont', 'sancarlos',' sanbruno'];
    
    public function __construct (
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        ShippingAddressManagement $shippingAddressManagement,
        ShippingMethodManagement $shippingMethodManagement,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository = null
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->addressRepository = $addressRepository ?: ObjectManager::getInstance()
            ->get(\Magento\Customer\Api\AddressRepositoryInterface::class);
        $this->shippingAddressManagement = $shippingAddressManagement;
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {
        
        if (isset($result['customerData']) && isset($result['customerData']['addresses'])) {
            $address = $result['customerData']['addresses'];
            $quote = $this->checkoutSession->getQuote();
            if($quote->getDeliveryMethod()=='local'){
                $filterAddr = [];
                foreach ($address as $key => $_addr) {
                    if(isset($_addr['region']) && isset($_addr['region']['region_code']) && $_addr['region']['region_code']=='CA') {
                        $city = str_replace(" ", "", strtolower($_addr['city']));
                        if(in_array($city, $this->allowedCity)){
                            $filterAddr[$key]=$_addr;
                        }
                    }
                }
                $result['customerData']['addresses'] = $filterAddr;
            }
            if ($quote->getDeliveryMethod() != 'storepickup') {
                $quote = $this->quoteFactory->create()->load($quote->getId());
                $shipping = $quote->getShippingAddress();
                //if($shipping->getFirstname()=='WINE' && $shipping->getLastname()=='STOP' ){

                    $addresses = $result['customerData']['addresses'];
                    $firstAddr = current($addresses);
                    if($firstAddr && is_array($firstAddr) && isset($firstAddr['id'])){
                        $addressId = $firstAddr['id'];
                        //$shippingAddress = $this->addressRepository->getById($addressId);
                        $addrModel = ObjectManager::getInstance()->get(\Magento\Customer\Model\AddressFactory::class);
                        $shippingAddress = $addrModel->create()->load($addressId);
                        
                        $old = $quote->getAddressesCollection()->getItemById($addressId)
                            ?? $quote->getShippingAddress();
                        
                        $old->addData($shippingAddress->getData())->setCustomerAddressId($shippingAddress->getId())->save();
                        /*$quote->addCustomerAddress($shippingAddress);
                        $shipping->setCustomerAddressData($shippingAddress);
                        $shipping->setCustomerAddressId($shippingAddress->getId())->save();
                        $quote->save();*/
                    }
                //}
            }

        }
        return $result;
    }
}
