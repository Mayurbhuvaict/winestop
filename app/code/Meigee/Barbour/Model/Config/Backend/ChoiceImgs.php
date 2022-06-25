<?php
namespace Meigee\Barbour\Model\Config\Backend;

class ChoiceImgs extends \Magento\Config\Model\Config\Backend\File
{
    public function afterLoad()
    {
        $val = $this->getValue();
        $value = empty($val) ? array() : unserialize($val);
        $this->setValue($value);
        return parent::afterLoad();
    }

    public function beforeSave()
    {
        $values = $this->getValue();
        $pathTmp = $this->getPath();
        $fieldConfig = $this->getFieldConfig();
        $baseUrl = isset($fieldConfig['base_url']) && isset($fieldConfig['base_url']['value']) ? $fieldConfig['base_url']['value'] : '';

        if (is_array($values) && !empty($values))
        {
            $value_arr = array();
            foreach($values AS $data_arr)
            {
                if (isset($data_arr['value']) && !empty($data_arr['value']))
                {
                    $value_arr[] = $data_arr['value'];
                }
                if (isset($data_arr['name']) && !empty($data_arr['name']))
                {
                    $this->setPath('notUse/'.$pathTmp);
                    $data_arr['value'] = $data_arr['name'];
                    $this->setValue($data_arr);
                    $value_arr[] = $baseUrl . parent::beforeSave()->getValue();
                }
            }
            $value = serialize($value_arr);
        }
        else
        {
            $value = '';
        }
        $this->setPath($pathTmp);
        $this->setValue($value);
		return $this;
    }

    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }

}
