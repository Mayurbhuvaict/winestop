<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Cron
 * @package Wyomind\Watchlog\Block\Adminhtml\System\Config\Form\Field
 */
class Cron extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = "<input class=' input-text'  type='hidden' id='" . $element->getHtmlId() . "' name='" . $element->getName() . "' value='" . $element->getEscapedValue() . "' '" . $element->serialize($element->getHtmlAttributes()) . "/>";

        $html .= "<table cellpadding='2' style='width:600px !important;width:auto;' class='wl-cron'>
            <thead> 
                <tr><th>Days of the week</th><th width='20'></th><th colspan='4'>Hours of the day</th></tr>
            </thead>
            <tr>
                <td width='300' align='left'>
                    <div>" . $this->checkbox("Monday", "d-Monday", "Monday", 'd') . "</div>
                    <div>" . $this->checkbox("Tuesday", "d-Tuesday", "Tuesday", 'd') . "</div>
                    <div>" . $this->checkbox("Wednesday", "d-Wednesday", "Wednesday", 'd') . "</div>
                    <div>" . $this->checkbox("Thursday", "d-Thursday", "Thursday", 'd') . "</div>
                    <div>" . $this->checkbox("Friday", "d-Friday", "Friday", 'd') . "</div>
                    <div>" . $this->checkbox("Saturday", "d-Saturday", "Saturday", 'd') . "</div>
                    <div>" . $this->checkbox("Sunday", "d-Sunday", "Sunday", 'd') . "</div>
                </td>
                <td width='175' class='morning-half'>
                    <div>" . $this->checkbox("00:00", "h-0000", "00:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("01:00", "h-0100", "01:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("02:00", "h-0200", "02:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("03:00", "h-0300", "03:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("04:00", "h-0400", "04:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("05:00", "h-0500", "05:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("06:00", "h-0600", "06:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("07:00", "h-0700", "07:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("08:00", "h-0800", "08:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("09:00", "h-0900", "09:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("10:00", "h-1000", "10:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("11:00", "h-1100", "11:00 AM", 'h') . "</div>
                </td>
                <td width='175' class='morning'>
                    <div>" . $this->checkbox("00:30", "h-0030", "00:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("01:30", "h-0130", "01:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("02:30", "h-0230", "02:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("03:30", "h-0330", "03:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("04:30", "h-0430", "04:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("05:30", "h-0530", "05:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("06:30", "h-0630", "06:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("07:30", "h-0730", "07:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("08:30", "h-0830", "08:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("09:30", "h-0930", "09:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("10:30", "h-1030", "10:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("11:30", "h-1130", "11:30 AM", 'h') . "</div>
                </td>
                <td width='175' class='afternoon-half'>
                    <div>" . $this->checkbox("12:00", "h-1200", "12:00 AM", 'h') . "</div>
                    <div>" . $this->checkbox("13:00", "h-1300", "01:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("14:00", "h-1400", "02:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("15:00", "h-1500", "03:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("16:00", "h-1600", "04:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("17:00", "h-1700", "05:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("18:00", "h-1800", "06:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("19:00", "h-1900", "07:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("20:00", "h-2000", "08:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("21:00", "h-2100", "09:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("22:00", "h-2200", "10:00 PM", 'h') . "</div>
                    <div>" . $this->checkbox("23:00", "h-2300", "11:00 PM", 'h') . "</div>
                </td>
                <td width='175' class='afternoon'>
                    <div>" . $this->checkbox("12:30", "h-1230", "12:30 AM", 'h') . "</div>
                    <div>" . $this->checkbox("13:30", "h-1330", "01:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("14:30", "h-1430", "02:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("15:30", "h-1530", "03:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("16:30", "h-1630", "04:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("17:30", "h-1730", "05:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("18:30", "h-1830", "06:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("19:30", "h-1930", "07:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("20:30", "h-2030", "08:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("21:30", "h-2130", "09:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("22:30", "h-2230", "10:30 PM", 'h') . "</div>
                    <div>" . $this->checkbox("23:30", "h-2330", "11:30 PM", 'h') . "</div>
                </td>
            </tr>
        </table>";

        $html .= "<script>
require([
    'jquery',
    'mage/mage'
], function ($) {
    $(function () {
        jQuery(document).ready(function () {

            if (jQuery('#" . $element->getHtmlId() . "').length > 0) {

                if (jQuery('#" . $element->getHtmlId() . "').val() === '') {
                    jQuery('#" . $element->getHtmlId() . "').val('{\"days\":[],\"hours\":[]}');
                }
                var cron = jQuery.parseJSON(jQuery('#" . $element->getHtmlId() . "').val());

                for (var i = 0; i < cron.days.length; i++) {
                    if (jQuery('#d-' + cron.days[i])) {
                        jQuery('#d-' + cron.days[i]).prop('checked', true);
                    }
                }

                for (var i = 0; i < cron.hours.length; i++) {
                    if (jQuery('#h-' + cron.hours[i].replace(':', ''))) {
                        jQuery('#h-' + cron.hours[i].replace(':', '')).prop('checked', true);
                    }
                }

                jQuery('.cron-box').on('click', function () {
                    var d = new Array();
                    jQuery('.cron-d-box').each(function () {
                        if (jQuery(this).prop('checked')) {
                            d.push(jQuery(this).val());
                        }
                    });
                    var h = new Array();
                    jQuery('.cron-h-box').each(function () {
                        if (jQuery(this).prop('checked')) {
                            h.push(jQuery(this).val());
                        }
                    });
                    jQuery('#" . $element->getHtmlId() . "').val(Object.toJSON({days: d, hours: h}));
                });
            }
        });
    });
});
</script>";

        $html .= $element->getAfterElementHtml();
        return $html;
    }

    /**
     * @param $value
     * @param $id
     * @param $label
     * @param $type
     * @return string
     */
    protected function checkbox($value, $id, $label, $type)
    {
        return '<label class="data-grid-checkbox-cell-inner">
            <input value="' . $value . '" id="' . $id . '" class="admin__control-checkbox cron-box cron-' . $type . '-box" type="checkbox">
            <label for="' . $id . '">' . $label . '</label>
        </label>';
    }
}
