<?php

namespace Winestop\Custom\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductLabel implements ArgumentInterface
{
    protected $_productloader;

    protected $stockRegistry;  

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) 
    {
        $this->stockRegistry = $stockRegistry;
        $this->_productloader = $_productloader;
    }
    public function isNewArrival($product)
    {
        //$product=$this->_productloader->create()->load($id);
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate   = $product->getNewsToDate();
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));
        $startDate = date('Y-m-d', strtotime($newsFromDate));
        $endDate = date('Y-m-d', strtotime($newsToDate));
        if(($currentDate >= $startDate) && ($currentDate <= $endDate))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getSpecialAttribute($product)
    {
        try {   
            $preSelectedValues = $product->getAttributeText('special_designations');

            if(is_array($preSelectedValues)){
                $selectedValues = $preSelectedValues;
            }else{
                $selectedValues[] = $preSelectedValues;
            }

            $finalSelectedValue = [];
            foreach ($selectedValues as $key => $specialAttrvalue) {
                if($specialAttrvalue == "Member Deals" || $specialAttrvalue == "Accessories"){
                    continue;
                }
                if($specialAttrvalue){
                    $finalSelectedValue[] = $specialAttrvalue;
                }
            }
            return $finalSelectedValue;
        } catch (\Exception $e) {

        }
        
    }
    public function getClassName($label)
    {
        $label = strtolower($label);
        $label = str_replace('&', '', $label);
        $label = str_replace(' ', '-', $label);
        $label = str_replace('--', '-', $label);
        return $label;
    }
    public function getStockItem($productId)
    {
        try {
        return $this->stockRegistry->getStockItem($productId)->getQty();
        } catch(\Exception $e) {
            return 0;
        }
    }
}
