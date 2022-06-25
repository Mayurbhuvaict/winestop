<?php
/**
 *
 * @author Ict Team
 * @copyright Copyright (c) 2018 Ict (http://icreativetechnologies.com/)
 * @package Ict_Stateshipping
 */

namespace Ict\Stateshipping\Model\Config\Source;

class Stateict
{
    private $region;

    public function __construct(\Magento\Directory\Model\Region $region)
    {
        $this->region = $region;
    }
    
    public function toOptionArray()
    {
        $region = $this->region->getCollection();
        foreach ($region as $key => $data) {
            $dt[$key]['value'] = $data['region_id'];
            $dt[$key]['label'] = $data['name'];
            $dt[$key]['title'] = $data['country_id'];
        }
        return $dt;
    }
}
