<?php
namespace Winestop\Custom\Plugin;

class Filter
{
    public function afterAddShippingData(\Magetrend\PdfTemplates\Model\Pdf\Filter $filter, $result)
    {
        if (isset($result['s_type'])) {
            $result['s_type'] = ucfirst($result['s_type']);
        } 
        return $result;
    }

}