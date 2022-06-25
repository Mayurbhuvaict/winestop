<?php
namespace Winestop\Custom\Plugin;

class Filter
{
    public function afterAddShippingData(\Magetrend\PdfTemplates\Model\Pdf\Filter $filter, $result)
    {   
        if($filter->getSource()->getOrder()) {
            // for invoice
            if($filter->getSource()->getOrder()->getShippingMethod() == 'storepickup_storepickup'){
                $result['s_fullname'] = '';
                $result['s_address'] = '';
                $result['s_region'] = '';
                $result['s_company'] = '';
                $result['s_street'] = 'NA';
                $result['s_city'] = '';
                $result['s_postcode'] = '';
                $result['s_country'] = '';
                $result['s_telephone'] = '';
                $result['s_type'] = '';
                return $result;
            } else {
                if (isset($result['s_type'])) {
                    $addresstype = $result['s_type'];
                    $result['s_type'] = $addresstype;
                    $result['s_type'] = "Address Type - " . ucfirst($result['s_type']);  
                }
                if(isset($result['s_city'])) {
                    $result['s_city'] = $result['s_city'].",";
                }
                return $result;
            }
        }
        if($filter->getSource()->getShippingMethod()) {
            // for order
            if ($filter->getSource()->getShippingMethod() == 'storepickup_storepickup') {
               $result['s_street'] = 'NA';
               $result['s_type'] = '';
            } else {
                if (isset($result['s_type'])) {
                    $addresstype = $result['s_type'];
                    $result['s_type'] = $addresstype;
                    $result['s_type'] = "Address Type - " . ucfirst($result['s_type']);  
                }
                if(isset($result['s_city'])) {
                    $result['s_city'] = $result['s_city'].",";
                } 
            }
            return $result;
        }
    }
}