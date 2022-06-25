<?php 
namespace Winestop\Gso\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
/**
 * 
 */
class Data extends AbstractHelper
{
	

   /**
   * Gso Account Number config path
   */
   const XML_PATH_GSO_ACCOUNT_NO = 'carriers/gsoshipping/account_no';

   /**
   * Gso Service Code config path
   */
   const XML_PATH_GSO_SERVICE_CODE = 'carriers/gsoshipping/gso_service_code';

   /**
   * Gso User Name config path
   */
   const XML_PATH_GSO_USERNAME = 'carriers/gsoshipping/gso_username';

   /**
   * Gso Password config path
   */
   const XML_PATH_GSO_PASSWORD = 'carriers/gsoshipping/gso_password';

   /**
   * Get Store zip
   */
   const XML_PATH_STORE_ZIP = 'general/store_information/postcode';

   /**
   * Get Store city
   */
   const XML_PATH_STORE_CITY = 'general/store_information/city';

   /**
   * Get Store street_line1
   */
   const XML_PATH_STORE_STREET = 'general/store_information/street_line1';

   /**
   * Get Store name
   */
   const XML_PATH_STORE_NAME = 'general/store_information/name';

   /**
   * Get Store State
   */
   const XML_PATH_STORE_State = 'general/store_information/region_id';
	
	/**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;

   /**
   * @var \Magento\Directory\Model\RegionFactory
   */
   protected $_regionFactory;

   /**
   * @var \Psr\Log\LoggerInterface
   */
   protected $logger;

   public function __construct(
   		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
   		\Magento\Directory\Model\RegionFactory $regionFactory,
   		\Psr\Log\LoggerInterface $logger
   	)
   {
      $this->scopeConfig = $scopeConfig;
      $this->_regionFactory = $regionFactory;
      $this->logger = $logger;
   }

    public function getStoreZip()
    {
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;      
      return $this->scopeConfig->getValue(self::XML_PATH_STORE_ZIP, $storeScope);
    }

    public function getStoreCity()
    {
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;      
      return $this->scopeConfig->getValue(self::XML_PATH_STORE_CITY, $storeScope);
    }

    public function getStoreStreet()
    {
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;      
      return $this->scopeConfig->getValue(self::XML_PATH_STORE_STREET, $storeScope);
    }

    public function getStoreName()
    {
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;      
      return $this->scopeConfig->getValue(self::XML_PATH_STORE_NAME, $storeScope);
    }

    public function getStoreStateCode()
    {
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;      
      $stat_id = $this->scopeConfig->getValue(self::XML_PATH_STORE_State, $storeScope);
      return $this->getShippingregion($stat_id);
    }

    public function getGsoAccountno()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
     	return $this->scopeConfig->getValue(self::XML_PATH_GSO_ACCOUNT_NO, $storeScope);
    }

    public function getGsoServiceCode()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
     	return $this->scopeConfig->getValue(self::XML_PATH_GSO_SERVICE_CODE, $storeScope);
    }

    public function getShippingregion($shipperRegionId)
    {
    	if (is_numeric($shipperRegionId)) {
          $shipperRegion = $this->_regionFactory->create()->load($shipperRegionId );
          $shipperRegionCode =$shipperRegion->getCode();
          return $shipperRegionCode;
       }
       else
       {
       	return false;
       }
    }

    public function getGsoUsername()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
     	return $this->scopeConfig->getValue(self::XML_PATH_GSO_USERNAME, $storeScope);
    }

    public function getGsoPassword()
    {
    	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
     	return $this->scopeConfig->getValue(self::XML_PATH_GSO_PASSWORD, $storeScope);
    }

    public function getGsoToken()
    {
        try {
                $username = $this->getGsoUsername();                
                $password = $this->getGsoPassword();
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
            $this->logger->critical($e->getMessage());
        }        

    }

    public function getGsoShipment($request)
    {
    	try {
    		$token = $this->getGsoToken();
	        if ($token['success'] == true) {
	            $token = $token['Token'];
	        }
	        $response = [];
	    	$curl = curl_init();
			  curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.gls-us.com/Rest/v1/Shipment",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($request),
			  CURLOPT_HTTPHEADER => array(
			    "Token: " . $token,
			    "Content-Type: application/json"
			  ),
			));

			$response = curl_exec($curl);
			$error = curl_error($curl);
      if ($error) {
             $response = $error;
      }
			curl_close($curl);
      return $response;
    	} catch (Exception $e) {
    		$this->logger->critical($e->getMessage());			
    	}
    }

    public function getGsoTrackShipment($trackingNumber)
    {
      try {
          $token = $this->getGsoToken();
          if ($token['success'] == true) {
              $token = $token['Token'];
          }
          $AccountNumber = $this->getGsoAccountno();
          $TrackingNumber = $trackingNumber;
          $url = "https://api.gls-us.com/Rest/v1/TrackShipment?AccountNumber=" . $AccountNumber . "&TrackingNumber=" . $TrackingNumber;
          $response = [];
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "Token: " . $token,
                "Cookie: SERVERID=EQXWSA06|X4+/z|X4+/X"
              ),
            ));

          $response = curl_exec($curl);
          $error = curl_error($curl);
          if ($error) {
                 $response = $error;
          }
          curl_close($curl);
          return $response;
      } catch (Exception $e) {
          $this->logger->critical($e->getMessage());
      }
    }
}