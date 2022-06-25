/**
 * @author Ict Team
 * @copyright Copyright (c) 2017 Ict (http://icreativetechnologies.com/)
 * @package Ict_Stateshipping
 */
require(['jquery'], function ($) {
    jQuery(window).load(function () {
        /** Start Free Shipping Disable/Enable State **/
        jQuery("#carriers_freeshipping_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_freeshipping_specificstate").attr("disabled","disabled");
                jQuery("#carriers_freeshipping_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_freeshipping_ictspecificcountry").val() != "") {
                    jQuery("#carriers_freeshipping_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_freeshipping_ictspecificcountry").removeAttr("disabled");
            }
        });
        
        jQuery("#carriers_freeshipping_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_freeshipping_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_freeshipping_sallowspecific").val() != 0) {
                    jQuery("#carriers_freeshipping_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_freeshipping_specificstate option").hide();
            jQuery("#carriers_freeshipping_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End Free Shipping Disable/Enable State **/
        
        /** Start Flat Rate Shipping Disable/Enable State **/
        
        jQuery("#carriers_flatrate_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_flatrate_specificstate").attr("disabled","disabled");
                jQuery("#carriers_flatrate_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_flatrate_ictspecificcountry").val() != "") {
                    jQuery("#carriers_flatrate_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_flatrate_ictspecificcountry").removeAttr("disabled");
            }
        });
        jQuery("#carriers_flatrate_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_flatrate_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_flatrate_sallowspecific").val() != 0) {
                    jQuery("#carriers_flatrate_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_flatrate_specificcountry").val(my_country);
            jQuery("#carriers_flatrate_specificstate option").hide();
            jQuery("#carriers_flatrate_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End Flat Rate Shipping Disable/Enable State **/
        
        /** Start Table Rate Shipping Disable/Enable State **/
        
        jQuery("#carriers_tablerate_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_tablerate_specificstate").attr("disabled","disabled");
                jQuery("#carriers_tablerate_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_tablerate_ictspecificcountry").val() != "") {
                    jQuery("#carriers_tablerate_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_tablerate_ictspecificcountry").removeAttr("disabled");
            }
        });
        jQuery("#carriers_tablerate_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_tablerate_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_tablerate_sallowspecific").val() != 0) {
                    jQuery("#carriers_tablerate_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_tablerate_specificstate option").hide();
            jQuery("#carriers_tablerate_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End Table Rate Shipping Disable/Enable State **/
        
        /** Start UPS Shipping Disable/Enable State **/
        
        jQuery("#carriers_ups_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_ups_specificstate").attr("disabled","disabled");
                jQuery("#carriers_ups_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_ups_ictspecificcountry").val() != "") {
                    jQuery("#carriers_ups_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_ups_ictspecificcountry").removeAttr("disabled");
            }
        });
        jQuery("#carriers_ups_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_ups_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_ups_sallowspecific").val() != 0) {
                    jQuery("#carriers_ups_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_ups_specificstate option").hide();
            jQuery("#carriers_ups_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End UPS Shipping Disable/Enable State **/
        
        /** Start FedEx Shipping Disable/Enable State **/
        
        jQuery("#carriers_fedex_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_fedex_specificstate").attr("disabled","disabled");
                jQuery("#carriers_fedex_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_fedex_ictspecificcountry").val() != "") {
                    jQuery("#carriers_fedex_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_fedex_ictspecificcountry").removeAttr("disabled");
            }
        });
        jQuery("#carriers_fedex_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_fedex_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_fedex_sallowspecific").val() != 0) {
                    jQuery("#carriers_fedex_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_fedex_specificstate option").hide();
            jQuery("#carriers_fedex_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End FedEx Shipping Disable/Enable State **/
        
        /** Start USPS Shipping Disable/Enable State **/
        
        jQuery("#carriers_usps_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_usps_specificstate").attr("disabled","disabled");
                jQuery("#carriers_usps_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_usps_ictspecificcountry").val() != "") {
                    jQuery("#carriers_usps_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_usps_ictspecificcountry").removeAttr("disabled");
            }
            
        });
        jQuery("#carriers_usps_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_usps_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_usps_sallowspecific").val() != 0) {
                    jQuery("#carriers_usps_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_usps_specificstate option").hide();
            jQuery("#carriers_usps_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End USPS Shipping Disable/Enable State **/
        
        /** Start DHL Shipping Disable/Enable State **/
        
        jQuery("#carriers_dhl_sallowspecific").change(function () {
            if (jQuery(this).val() == 0) {
                jQuery("#carriers_dhl_specificstate").attr("disabled","disabled");
                jQuery("#carriers_dhl_ictspecificcountry").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_dhl_ictspecificcountry").val() != "") {
                    jQuery("#carriers_dhl_specificstate").removeAttr("disabled");
                }
                jQuery("#carriers_dhl_ictspecificcountry").removeAttr("disabled");
            }
            
        });
        jQuery("#carriers_dhl_ictspecificcountry").change(function () {
            if (jQuery(this).val() == "") {
                jQuery("#carriers_dhl_specificstate").attr("disabled","disabled");
            } else {
                if (jQuery("#carriers_dhl_sallowspecific").val() != 0) {
                    jQuery("#carriers_dhl_specificstate").removeAttr("disabled");
                }
            }
            var my_country = jQuery(this).val();
            jQuery("#carriers_dhl_specificstate option").hide();
            jQuery("#carriers_dhl_specificstate option[title='"+my_country+"']").show();
        });
        
        /** End DHL Shipping Disable/Enable State **/
        
        var my_country_dhl = jQuery("#carriers_dhl_ictspecificcountry").val();
            jQuery("#carriers_dhl_specificstate option").hide();
            jQuery("#carriers_dhl_specificstate option[title='"+my_country_dhl+"']").show();
        var my_country_fedex = jQuery("#carriers_fedex_ictspecificcountry").val();
            jQuery("#carriers_fedex_specificstate option").hide();
            jQuery("#carriers_fedex_specificstate option[title='"+my_country_fedex+"']").show();
        var my_country_ups = jQuery("#carriers_ups_ictspecificcountry").val();
            jQuery("#carriers_ups_specificstate option").hide();
            jQuery("#carriers_ups_specificstate option[title='"+my_country_ups+"']").show();
        var my_country_tablerate = jQuery("#carriers_tablerate_ictspecificcountry").val();
            jQuery("#carriers_tablerate_specificstate option").hide();
            jQuery("#carriers_tablerate_specificstate option[title='"+my_country_tablerate+"']").show();
        var my_country_flatrate = jQuery("#carriers_flatrate_ictspecificcountry").val();
            jQuery("#carriers_flatrate_specificstate option").hide();
            jQuery("#carriers_flatrate_specificstate option[title='"+my_country_flatrate+"']").show();
        var my_country_freeshipping = jQuery("#carriers_freeshipping_ictspecificcountry").val();
            jQuery("#carriers_freeshipping_specificstate option").hide();
            jQuery("#carriers_freeshipping_specificstate option[title='"+my_country_freeshipping+"']").show();
        var my_country_usps = jQuery("#carriers_usps_ictspecificcountry").val();
            jQuery("#carriers_usps_specificstate option").hide();
            jQuery("#carriers_usps_specificstate option[title='"+my_country_usps+"']").show();
            
        if (jQuery("#carriers_dhl_sallowspecific").val() == 0 || jQuery("#carriers_dhl_sallowspecific").val() == "") {
            jQuery("#carriers_dhl_specificstate").attr("disabled","disabled");
            jQuery("#carriers_dhl_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_fedex_sallowspecific").val() == 0 || jQuery("#carriers_fedex_sallowspecific").val() == "") {
            jQuery("#carriers_fedex_specificstate").attr("disabled","disabled");
            jQuery("#carriers_fedex_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_ups_sallowspecific").val() == 0 || jQuery("#carriers_ups_sallowspecific").val() == "") {
            jQuery("#carriers_ups_specificstate").attr("disabled","disabled");
            jQuery("#carriers_ups_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_tablerate_sallowspecific").val() == 0 || jQuery("#carriers_tablerate_sallowspecific").val() == "") {
            jQuery("#carriers_tablerate_specificstate").attr("disabled","disabled");
            jQuery("#carriers_tablerate_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_flatrate_sallowspecific").val() == 0 || jQuery("#carriers_flatrate_sallowspecific").val() == "") {
            jQuery("#carriers_flatrate_specificstate").attr("disabled","disabled");
            jQuery("#carriers_flatrate_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_freeshipping_sallowspecific").val() == 0 || jQuery("#carriers_freeshipping_sallowspecific").val() == "") {
            jQuery("#carriers_freeshipping_specificstate").attr("disabled","disabled");
            jQuery("#carriers_freeshipping_ictspecificcountry").attr("disabled","disabled");
        }
        if (jQuery("#carriers_usps_sallowspecific").val() == 0 || jQuery("#carriers_usps_sallowspecific").val() == "") {
            jQuery("#carriers_usps_specificstate").attr("disabled","disabled");
            jQuery("#carriers_usps_ictspecificcountry").attr("disabled","disabled");
        }
    });
});