<?php
namespace Meigee\Barbour\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class InputColorMultiple extends Field
{

    public function __construct(
        Context $context,
		\Meigee\Barbour\Block\Frontend\CustomDesign $customDesign,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->_customDesign = $customDesign;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addClass('meigee-color-picker');
		$default_name = $element->getName();
		$element_vals = $element->getValue();
		
		$start = strpos($default_name, '[fields]');
		$length = strlen($default_name);
		$end = strpos($default_name, '[value]');
		$end = $length - $end + 1;
		$val_res = substr($default_name, -$start, -$end);
		
		if($element_vals != null) {
			$element_vals = explode('; ', $element_vals);
		} else {
			$g_start = strpos($default_name, 'groups');
			$g_end = strpos($default_name, '[fields]');
			$g_end = $length - $g_end + 1;
			$g_res = substr($default_name, $g_start, -$g_end);
			$group = str_replace('groups[', '', $g_res);
			
			$f_start = strpos($default_name, '[fields]');
			$f_end = strpos($default_name, '[value]');
			$f_end = $length - $f_end + 1;
			$f_res = substr($default_name, $f_start, -$f_end);
			$field = str_replace('[fields][', '', $f_res);
			
			$config = str_replace('_'.$group.'_'.$field, DIRECTORY_SEPARATOR.$group.DIRECTORY_SEPARATOR.$field, $element->getId());
			
			$this->_customDesign->removeDataByPath($config);
			
		}	
		
		$ids = array(null => '_d', 'Hover' => '_h', 'Active' => '_a', 'Focus' => '_f');
		$el_id = $element->getId();
		$html = '<div class="input-group meigee-group">';
		$i = 0;
		$label = '';
		
		
		foreach($ids as $key => $id) {
			$new_name = str_replace($val_res, $val_res.$id, $default_name);
			$element->setName($new_name);
			if($key != null) {
				$label = '<label>'.$key.'</label>';
			}
			$element->setId($el_id.$id);
			if(isset($element_vals[$i])) {
				$element->setValue($element_vals[$i]);
				$html .= "<div class='input-box'>".$label."<input type='text' id='".$element->getId()."' class='meigee-color-picker multiple' name='".$element->getName()."' value='".$element_vals[$i]."'></div>";
			}
			$i++;
		}
		$html .= "<input type='hidden' name='".$default_name."' class='multiple-result' value=''>";
		$html .= "</div>";
		
        return $html;
    }
}
