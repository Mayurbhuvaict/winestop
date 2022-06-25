<?php
namespace Meigee\Barbour\Block\Adminhtml\System\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class InputRadio extends \Magento\Config\Block\System\Config\Form\Field
{
    static $isJsShowed = false;
    static $isCssShowed = false;
    private function getCss()
    {
        if (self::$isCssShowed)
        {
            return '';
        }
        self::$isCssShowed = true;
        return "
              <style>
            .display-none{ display: none!important; }
			.meigee-thumb-horizontal {float: left; width: 25%; padding: 6px; box-sizing: border-box; -webkit-box-sizing: border-box;}
			.meigee-thumb-vertical + .meigee-thumb-vertical {margin-top: 12px;}
			.meigee-thumb-horizontal label,
			.meigee-thumb-vertical label {
				padding: 2px; border:
				solid 1px transparent;
				display: block;
				cursor: pointer;
				transition: border-color 300ms ease;
				-moz-transition: border-color 300ms ease;
				-webkit-transition: border-color 300ms ease;
			}
			.meigee-thumb-horizontal label:hover,
			.meigee-thumb-horizontal label.active,
			.meigee-thumb-vertical label:hover,
			.meigee-thumb-vertical label.active {border-color: #2991d6;}
        </style>";
    }

    private function getJs()
    {
        if (self::$isJsShowed)
        {
            return '';
        }
        self::$isJsShowed = true;

        return '<script> type="text/javascript">
                require(["jquery"], function(jQuery)
                {
                    jQuery(document).ready( function()
                    {
                        jQuery(".meigee-thumb-radio-input").change(function()
                        {
                            jQuery(this).closest(".meigee-thumb-radio").find("label.active").removeClass("active");
                            jQuery(this).closest("label").addClass("active");
                        })
                    });
                });
                </script>';
    }


    protected function _getElementHtml(AbstractElement $element)
    {
        $value = $element->getValue();
        $values = $element->getValues();
        $element->addClass('meigee-thumb-radio-input');
        $element->addClass('display-none');
        $typeClass = $this->getTypeClass();

        $html = $this->getCss();
        $html .= $this->getJs();

	 $html .= '<div class="clearfix meigee-thumb-radio">';
      foreach ($values AS $val)
        {
            $isChecked = $val['value'] == $value;
            $element->setValue($val['value']);

            $element->setChecked($isChecked);
            $html .= '
                    <div class="'.$typeClass.'">
                        <label '.($isChecked?"class='active'":"").'>
                            <div>
                                <h5>'.$val['label'].'</h5>
                            </div>
                            <div>
                                <img src="'. $this->getViewFileUrl($val['img']).'" />
                            </div>
                            ' . $element->getElementHtml() . '
                        </label>
                    </div>';
        }
        $html .= '</div>';
        return $html;
    }

    abstract function getTypeClass() ;
}
