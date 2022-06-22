<?php 
namespace Magecomp\Extrafee\Model\Source;

class AddressType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const ADDRESS_TYPE_CODE = 'type';
    const ADDRESS_RESIDENTIAL_TEXT = 'residential';
    const ADDRESS_BUSSINESS_TEXT = 'business';

    public function getAllOptions()
    {
        $options = array();
        $options =[
            [
                'value' => self::ADDRESS_RESIDENTIAL_TEXT,
                'label' => __(ucwords(self::ADDRESS_RESIDENTIAL_TEXT))
            ],
            [
                'value' => self::ADDRESS_BUSSINESS_TEXT,
                'label' => __(ucwords(self::ADDRESS_BUSSINESS_TEXT))
            ]
        ];

        return $options;
    }

    public function toOptionArray()
    {
        $_options = $this->getAllOptions();
        $options=[];
        foreach($_options as $op){
            $options[$op['value']] = $op['label'];
        }
        return $options;
    }
    
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
?>