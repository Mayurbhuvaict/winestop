<?php 

namespace Winestop\Custom\Plugin;

class ChangeDescOrder
{
    public function afterGetWidgetOptionsJson(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        $result
    ) {
        
        $result = json_decode($result,true);
        if ($subject->getCurrentOrder() == 'latest') {
            $result['productListToolbarForm']['directionDefault'] = 'desc';
        }
       return json_encode($result);
    }   
}