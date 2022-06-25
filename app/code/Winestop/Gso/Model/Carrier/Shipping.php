<?php
namespace Winestop\Gso\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
/**
 * 
 */
class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
	
	 
  /**
     * @var string
     */
    protected $_code = 'gsoshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
   * @var \Winestop\Gso\Helper\Data
   */
   protected $dataHelper;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Winestop\Gso\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    public function isTrackingAvailable(){
      return true;
    }
    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $allow_state = array('AZ', 'CA', 'ID', 'NV', 'NM', 'OR', 'WA');
        $DeliveryState = (string)$request->getData('dest_region_code');        
        if (in_array($DeliveryState, $allow_state)) {
          $token = $this->getGsoToken();
        
          if (is_array($token) && $token['success'] == true) {   

              $token = $token['Token'];
              $DestinationZip = $request->getData('dest_postcode');
              $PackageWeight = (int)$request->getData('package_weight');
              if ($token != '' && $DestinationZip != '') {
                  if ($PackageWeight == '') {
                      $PackageWeight = 0;
                   }  
                  $shippingrate = $this->getGsoShippingRate($token,$DestinationZip,$PackageWeight);                  
                  if ($shippingrate['ErrorCount'] == 0) {                     
                      $TotalCharge = $shippingrate['DeliveryServiceTypes'][0]['ShipmentCharges']['TotalCharge'];          
                  
                      /** @var \Magento\Shipping\Model\Rate\Result $result */
                      $result = $this->_rateResultFactory->create();

                      /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                      $method = $this->_rateMethodFactory->create();

                      $method->setCarrier($this->_code);
                      $method->setCarrierTitle($this->getConfigData('title'));

                      $method->setMethod($this->_code);
                      $method->setMethodTitle($this->getConfigData('name'));

                      $amount = $this->getShippingPrice();
                      $amount = $amount + $TotalCharge;

                      $method->setPrice($amount);
                      $method->setCost($amount);

                      $result->append($method);

                      return $result;
                  }
              }
          }
          else
          {
              return false;
          }
        }
    }

    public function getGsoToken()
    {
        try {
                $username = $this->getConfigData('gso_username');                
                $password = $this->getConfigData('gso_password');
                $token = false;
                $response = [];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.gls-us.com/Rest/v1/token",
                    CURLOPT_HEADER => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "username: " . $username,
                        "password: " . $password,
                    ),
                ));

                $response = curl_exec($curl);
                $error = curl_error($curl);

                curl_close($curl);

                if ($error) {
                    $response = ['error' => 'true', 'message' => $error];
                } else {
                    
                    $responseArray = explode("\n", $response);
                    foreach ($responseArray as $responseKey => $responseValue) {
                        $valueArray = explode(':', $responseValue);
                        if ($valueArray[0] == 'Token') {
                            $token = $valueArray[1];
                            $token = str_replace(" ", "", $token);            
                            $token = substr($token, 0, -1);
                            $response = ['success' => 'true', 'Token' => $token];            
                        }
                    }
                }
                return $response;
        } catch (Exception $e) {
            $logger->critical($e->getMessage());
        }        

    }

    public function getGsoShippingRate($token,$DestinationZip,$PackageWeight)
    {
        try {    
            $date = $this->getGsoDate();
            $OriginZip = $this->dataHelper->getStoreZip();
            $AccountNumber = (int)$this->getConfigData('account_no');
            $data = array(
                'AccountNumber' => $AccountNumber,
                'OriginZip' => $OriginZip,
                'DestinationZip' => $DestinationZip,
                'ShipDate' => $date,
                'PackageWeight' => $PackageWeight
                );                   
            $curl = curl_init();
            //$custom = 'akQgNcsbpjVse8XDyaStgcwbxnmCwgDcwc5Ayd/ZOH/cDUFC6MdEJyUaxwDoKtusABZZrwvjnNZ6RY2UaahjOeh8HN45Ukq7';
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.gls-us.com/Rest/v1/RatesAndTransitTimes",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>json_encode($data),
              CURLOPT_HTTPHEADER => array(
                "Token: " . $token,
                "Content-Type: application/json"
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);            
            $response = json_decode($response, true);
            return $response;
        } catch (Exception $e) {
                $logger->critical($e->getMessage());
        }
    }

    public function getGsoDate()
    {
      $new_year = '01/01';
      $memoriyal_day = date('m/d', strtotime("last Monday of May " . date('y')));
      $independence_day = '07/04';
      $Labor_day = date('m/d', strtotime("first Monday of Sep " . date('y')));
      $Thanksgiving_day = date('m/d', strtotime("fourth Thursday of Nov " . date('y')));
      $Christmas_day = '12/25';  

      $holidaylist = array($new_year, $memoriyal_day, $independence_day , $Labor_day, $Thanksgiving_day, $Christmas_day, 'Sat', 'Sun');
      
      if (!in_array(date('m/d', mktime(0, 0, date('m'), date('d') + 1)), $holidaylist) && !in_array(date('D', mktime(0, 0, 0, date('m'), date('j') + 1, date('Y'))), $holidaylist)) {
        $date = date('m/j/Y', mktime(0, 0, 0, date('m'), date('j') + 1, date('Y')));
      }
      elseif (!in_array(date('m/d', mktime(0, 0, date('m'), date('d') + 2)), $holidaylist) && !in_array(date('D', mktime(0, 0, 0, date('m'), date('j') + 2, date('Y'))), $holidaylist)) {
        $date = date('m/j/Y', mktime(0, 0, 0, date('m'), date('j') + 2, date('Y')));
      }
      elseif (!in_array(date('m/d', mktime(0, 0, date('m'), date('d') + 3)), $holidaylist) && !in_array(date('D', mktime(0, 0, 0, date('m'), date('j') + 3, date('Y'))), $holidaylist)) {
        $date = date('m/j/Y', mktime(0, 0, 0, date('m'), date('j') + 3, date('Y')));
      }
      elseif (!in_array(date('m/d', mktime(0, 0, date('m'), date('d') + 4)), $holidaylist) && !in_array(date('D', mktime(0, 0, 0, date('m'), date('j') + 4, date('Y'))), $holidaylist)) {
        $date = date('m/j/Y', mktime(0, 0, 0, date('m'), date('j') + 4, date('Y')));
      }
      elseif (!in_array(date('m/d', mktime(0, 0, date('m'), date('d') + 5)), $holidaylist) && !in_array(date('D', mktime(0, 0, 0, date('m'), date('j') + 5, date('Y'))), $holidaylist)) {
        $date = date('m/j/Y', mktime(0, 0, 0, date('m'), date('j') + 5, date('Y')));
      }
      else {
        $date = date('m/j/Y');
     }
      return $date;
    }
}