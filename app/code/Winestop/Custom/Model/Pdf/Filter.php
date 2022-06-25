<?php
 
namespace Winestop\Custom\Model\Pdf;
 
class Filter extends \Magetrend\PdfTemplates\Model\Pdf\Filter
{
    public function getData(){
        return $data;
    }
    /**
     * Returns billing data
     *
     * @param $data
     * @return mixed
     */
    public function addShippingData($data)
    {
        $data['s_fullname'] = '';
        $data['s_address'] = '';
        $data['s_region'] = '';
        $data['s_company'] = '';
        $data['s_street'] = '';
        $data['s_city'] = '';
        $data['s_postcode'] = '';
        $data['s_country'] = '';
        $data['s_telephone'] = '';
        $source = $this->getSource();
        $streetLines = explode(' ', $data['street']);
        $updatedStreet = '';
        if (count($streetLines) > 1) {
            foreach ($streetLines as $key => $streetLine) {
                $updatedStreet .= trim($streetLine) . " ";
            }
        }
        $data['street'] = $updatedStreet;
        $shippingAddress = $source->getShippingAddress();
        if (!$shippingAddress) {
            return $data;
        }

        $shippingData = $shippingAddress->getData();
        if (empty($shippingData)) {
            return $data;
        }

        foreach ($shippingData as $key => $value) {
            if (is_object($value)) {
                continue;
            }
            $data['s_'.$key] = $value;
        }
        $streetLines = explode(' ', $data['s_street']);
        $updatedStreet = '';
        if (count($streetLines) > 1) {
            foreach ($streetLines as $key => $streetLine) {
                $updatedStreet .= trim($streetLine) . " ";
            }
        }
        $data['s_street'] = $updatedStreet;
        $middleName = $shippingAddress->getMiddlename();
        if (!empty($middleName)) {
            $middleName = ' '. $middleName;
        }
        $data['s_fullname'] = $shippingAddress->getFirstname().$middleName.' '.$shippingAddress->getLastname();
        if (isset($data['s_country_id']) && !empty($data['s_country_id'])) {
            $country = $this->countryFactory->create()->loadByCode($data['s_country_id']);
            $data['s_country'] = $country->getName();
        }
        $data['s_address'] = $this->getFormatedAddress($shippingAddress);
        return $data;
    }
}
