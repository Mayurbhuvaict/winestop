<?php
namespace Winestop\Shopby\Model;
    
class TypeOption implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->eavConfig = $eavConfig;

    }
    public function toOptionArray()
    {   
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'color');
        $options = $attribute->getSource()->getAllOptions();
        $i = 0;
        foreach($options as $key => $value)
        {
            $options[$i]['value'] = $value['label'];
            $i++;
        }
        return $options;
    }
}
