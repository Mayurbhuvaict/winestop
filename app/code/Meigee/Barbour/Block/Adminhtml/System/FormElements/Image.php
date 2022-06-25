<?php

namespace Meigee\Barbour\Block\Adminhtml\System\FormElements;

class Image extends \Magento\Framework\Data\Form\Element\Image
{
/*
    public function getElementHtml()
    {
        $realValue = $this->getValue();
        $this->setValue('data');
        $html = parent::getElementHtml();
        $this->setValue($realValue);
        
        return $html;
    }
*/
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
                                ChoiceCssCounter++;
                                clone.find("[name*=__origin__]").each(function()
                                {
                                    var name = jQuery(this).attr("name").replace("__origin__", ChoiceCssCounter)
                                    jQuery(this).attr("name", name);
                                })
                                jQuery("#choice-imgs-table").append(clone);
                        });
                        var ChoiceCssCounter = 1;
                });
        </script>';
    }
    
    private function _getFormtRow($isHidden = true)
    {
        $trAttr = $isHidden ? 'style="display:none;" id="choice-imgs-origin-row"' : '';     

        return '<tr '.$trAttr.'>
                    <td>'.parent::getElementHtml().'</td>
                    <td>
                        <button class="action-default scalable delete choice-imgs-delete-btn"  type="button" title="'.__('Remove').'" >
                            <span>'.__('Remove').'</span>
                        </button>
                    </td>
                </tr>';
    }
    
    public function getElementHtml()
    {
        $name = $this->getName();

        $htmlId = $this->getHtmlId();
        $this->setName($name.'[__origin__]');
        $valArr = $this->getValue();
        if(!is_array($valArr)){
            $valArr = array($valArr);
        }
        $this->setValue('');
        $html = '<table  id="choice-imgs-table">
                <tr>
                    <th>'.__('File').'</th>
                    <th></th>
                </tr>'. $this->_getFormtRow() ;
        
        $valI = 0;
        foreach ($valArr AS $val)
        {
            $this->setName($name.'[s'.$valI.']');
            $this->setValue($val);
            $this->setHtmlId($htmlId.'-'.$valI);
            $html .= $this->_getFormtRow(false);
            $valI++;
        }
        $html .= '</table>
        <button id="choice-imgs-add-row" class="action-default scalable add" type="button" title="'.__('Add').'" >
            <span>'.__('Add').'</span>
        </button>';        
        return  $html . $this->_getCss(). $this->_getJs();
    }

    
}