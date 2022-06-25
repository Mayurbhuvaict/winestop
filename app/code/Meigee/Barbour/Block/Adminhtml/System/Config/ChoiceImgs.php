<?php

namespace Meigee\Barbour\Block\Adminhtml\System\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ChoiceImgs extends \Magento\Config\Block\System\Config\Form\Field
{
    private $_element;
/*
    private function _getCss()
    {
	return '
	    <style>
	    #choice-imgs-table .delete-image {display:none}
	    </style>
	';
    }

    private function _getJs()
    {
        return '
        <script type="text/javascript">
                require(["jquery"], function(jQuery)
				{
                        jQuery(".choice-imgs-delete-btn").click( function()  { jQuery(this).closest("tr").remove(); });
						jQuery("#choice-imgs-add-row").click( function()
						{
								var clone = jQuery("#choice-imgs-origin-row").clone(true).attr("id", "").attr("style", "");
								clone.find("[name*=__origin__]").each(function()
								{
									jQuery(this).attr("name", name);
								})
								jQuery("#choice-imgs-table").append(clone);
                        });
                });
        </script>';
    }

    private function _getFormtRow($isHidden = true)
    {
        $trAttr = $isHidden ? 'style="display:none;" id="choice-imgs-origin-row"' : '';

        return '<tr '.$trAttr.'>
					<td>'.$this->_element->getElementHtml().'</td>
					<td>
						<button class="action-default scalable delete choice-imgs-delete-btn"  type="button" title="'.__('Remove').'" >
							<span>'.__('Remove').'</span>
						</button>
					</td>
				</tr>';
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $this->_element = $element;
        $name = $this->_element->getName();
        $this->_element->setName($name.'[]');

        $valArr = $this->_element->getValue();

        $html = '<table  id="choice-imgs-table">
				<tr>
					<th>'.__('File').'</th>
					<th></th>
				</tr>'. $this->_getFormtRow() ;


$valArr = ['ddd', 'sss'];

        foreach ($valArr AS $val)
        {
            $this->_element->setValue($val);
            $html .= $this->_getFormtRow(false);
        }
        $html .= '</table>
		<button id="choice-imgs-add-row" class="action-default scalable add" type="button" title="'.__('Add').'" >
			<span>'.__('Add').'</span>
		</button>';
        return  $html . $this->_getCss(). $this->_getJs();
    }

*/



















}


