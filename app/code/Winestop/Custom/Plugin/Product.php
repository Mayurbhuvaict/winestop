<?php 
namespace Winestop\Custom\Plugin;

/**
 * 
 */
class Product
{
	
	public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
       // return $result + 100;
    	return $result;
    }
}