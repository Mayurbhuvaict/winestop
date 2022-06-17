<?php

namespace Meigee\Barbour\Block\Adminhtml\System\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class CheckboxSwitch extends \Magento\Config\Block\System\Config\Form\Field
{
    static $_isCssUsed = false;
    static $_isJsUsed = false;

    protected $invert = false;

    private function _getCss()
    {
	if (self::$_isCssUsed)
	{
	    return '';
	}
	self::$_isCssUsed = true;
	return '<style>
    body .onoffswitch {
        position: relative; width: 120px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    body .onoffswitch-checkbox {
        display: none!important;
    }
    body .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border-radius: 20px;
    }
    body .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    body .onoffswitch-inner .label-on,
    body .onoffswitch-inner .label-off {
        display: block; float: left; width: 50%; height: 33px; padding: 0; line-height: 33px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    body .onoffswitch-inner .label-on {
/*        content: "ON"; */
        padding-left: 20px;
        background-color: #6bba14; color: #FFFFFF;
    }
    body .onoffswitch-inner .label-off {
/*        content: "OFF";   */
        padding-right: 20px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    body .onoffswitch-switch {
        display: block; width: 15px; margin: 9px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 86px;
         border-radius: 21px;
        transition: all 0.3s ease-in 0s;
    }
    body .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    body .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px;
    }
    .onoffswitch-value { display: none!important; }
</style>';
    }

    private function _getJs()
    {
	if (self::$_isJsUsed)
	{
	    return '';
	}
	self::$_isJsUsed = true;
	return '
<script type="text/javascript">//<![CDATA[
    function updateOnOffSwitch(el)
    {
	var useid = jQuery(el).attr("data-useid");

	if (jQuery(el).hasClass("onoffswitch-checkbox-invert"))
	{
	    var val = jQuery(el).attr("checked") ? "0" : "1";
	}
	else
	{
	    var val = jQuery(el).attr("checked") ? "1" : "0";
	}
	document.getElementById(useid).value = val;


	if(document.createEventObject)
	{
	  var evt = document.createEventObject();
	  document.getElementById(useid).fireEvent("onchange", evt);
	}
	else
	{
	  var evt = document.createEvent("HTMLEvents");
	  evt.initEvent("change", false, true);
	  document.getElementById(useid).dispatchEvent(evt);
	}
    }

                require(["jquery"], function(jQuery)
                {
                    jQuery(document).ready( function()
                    {
                        jQuery(".onoffswitch-value").each(function()
                        {
                            jQuery(this).attr("checked","checked");
                        })
                    });
                })
//]]></script>';
    }


    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addClass('onoffswitch-value');
        if ($this->invert)
        {
	    $invertClass = 'onoffswitch-checkbox-invert' ;
	    $checked = (bool)$element->getValue() ? '' : 'checked="checked"';
        }
        else
        {
	    $invertClass = '';
	    $checked = (bool)$element->getValue() ? 'checked="checked"' :'';
        }
        $element->setType('checkbox');
        $element->setChecked(true);
        return $this->_getCss() . $this->_getJs() . '
                <div class="onoffswitch">
		    <input id="'.$element->getId().'_onoffswitch" class="onoffswitch-checkbox ' . $invertClass .'" type="checkbox" onChange="updateOnOffSwitch(this)" data-useid="'.$element->getId().'"  '.$checked.' />
                    <label class="onoffswitch-label" for="'.$element->getId().'_onoffswitch">
                        <span class="onoffswitch-inner">
							<span class="label-on">'.$this->getOnLabel().'</span>
							<span class="label-off">'.$this->getOffLabel().'</span>
						</span>
                        <span class="onoffswitch-switch"></span>
                    </label>

                      '.$element->getElementHtml().'
                </div>';
    }

    abstract function getOnLabel();
    abstract function getOffLabel();

}
