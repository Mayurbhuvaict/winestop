<?php 
namespace Magecomp\Extrafee\Plugin\Model\Quote;

use Magento\Framework\Webapi\Rest\Request;

class Method
{
    protected $_customerSession;
    protected $_notallowCarrier = ['storepickup','localshipping'];
    protected $gsoShipping = 'gsoshipping';
    protected $_apiRequest;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magecomp\Extrafee\Helper\Data $helper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Request $request
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutsession = $checkoutSession;
        $this->_helper = $helper;
        $this->priceCurrency = $priceCurrency;
        $this->_apiRequest = $request;
    }


    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return $this
     */
    public function aroundSetPrice(
      \Magento\Quote\Model\Quote\Address\RateResult\Method $subject,
      \Closure $proceed,
      $price
    ) {
      
      if(!in_array($subject->getCarrier(), $this->_notallowCarrier)){
        $quote = $this->_checkoutsession->getQuote(); 
        $handlingfee = $this->_helper->getHandlingFee($quote);
        $price = $price + $handlingfee;
        /*if($subject->getCarrier()==$this->gsoShipping){
          $addType = $this->getAddressType();
          if($addType){
            $adultRate = $this->_helper->getGlsAdultPrice();
            if(isset($adultRate[$addType])){
              $price = $price + $adultRate[$addType]['rate'] + $adultRate[$addType]['adultfee'];
            }
          }

        }
        */

      }
      $subject->setData('price', $this->priceCurrency->round($price));
      
      return $this;
    }

    public function getAddressType(){
        $post = $this->_apiRequest->getBodyParams();
        $type='';
        $shippingAddress = $this->_checkoutsession->getQuote()->getShippingAddress();
        if($shippingAddress->getType()){
            $type=$shippingAddress->getType();
        }
        if(isset($post['address'])){
            if(isset($post['address']['custom_attributes'])){
                foreach($post['address']['custom_attributes'] as $att){
                    if(strtolower($att['attribute_code'])==strtolower(\Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_TYPE_CODE)){
                        $type=$att['value'];
                    }
                }
            }
        }
        if(isset($post['shippingAddress'])){
            if(isset($post['shippingAddress']['customAttributes'])){
                $customAttr = $post['shippingAddress']['customAttributes'];
                foreach($customAttr as $att){
                    if(strtolower($att['attribute_code'])==strtolower(\Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_TYPE_CODE)){
                        $type=$att['value'];
                    }
                }
            }
        }
        return $type;
    }
}