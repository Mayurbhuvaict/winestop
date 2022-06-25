define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        if (config.isEnable == 1) {
            jQuery("#row_carriers_flatrate_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_freeshipping_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_tablerate_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_ups_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_usps_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_fedex_specificcountry").attr("hidden","true");
            jQuery("#row_carriers_dhl_specificcountry").attr("hidden","true");
        } else {
            jQuery("#row_carriers_flatrate_ictspecificcountry").remove();
            jQuery("#row_carriers_flatrate_specificstate").remove();
            jQuery("#row_carriers_freeshipping_ictspecificcountry").remove();
            jQuery("#row_carriers_freeshipping_specificstate").remove();
            jQuery("#row_carriers_tablerate_ictspecificcountry").remove();
            jQuery("#row_carriers_tablerate_specificstate").remove();
            jQuery("#row_carriers_ups_ictspecificcountry").remove();
            jQuery("#row_carriers_ups_specificstate").remove();
            jQuery("#row_carriers_usps_ictspecificcountry").remove();
            jQuery("#row_carriers_usps_specificstate").remove();
            jQuery("#row_carriers_fedex_ictspecificcountry").remove();
            jQuery("#row_carriers_fedex_specificstate").remove();
            jQuery("#row_carriers_dhl_ictspecificcountry").remove();
            jQuery("#row_carriers_dhl_specificstate").remove();
        }
    }
});