/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(["jquery"], function ($) {
    "use strict";
    return {
        waitFor: function (elt, callback) {
            let initializer = null;
            initializer = setInterval(function () {
                if ($(elt).length > 0) {
                    setTimeout(callback, 500);
                    clearInterval(initializer);
                }
            }, 200);
        },
        /**
         * Load the selected days and hours
         */
        loadExpr: function () {
            this.waitFor("#cron_expr", function () {
                if ($("#cron_expr").val() === "") {
                    $("#cron_expr").val("{}");
                }
                let val = $.parseJSON($("#cron_expr").val());
                if (val !== null) {
                    if (val.days)
                        $.each(val.days, function (index, elt) {
                            $("#d-" + elt).parent().addClass("selected");
                            $("#d-" + elt).prop("checked", true);
                        });
                    if (val.hours)
                        $.each(val.hours, function (index, elt) {
                            let hour = elt.replace(":", "");
                            $("#h-" + hour).parent().addClass("selected");
                            $("#h-" + hour).prop("checked", true);
                        });
                }
            }.bind(this));
        },
        /**
         * Update the json representation of the cron schedule
         */
        updateExpr: function () {
            let days = new Array();
            let hours = new Array();
            $(".cron-box.day").each(function () {
                if ($(this).prop("checked") === true) {
                    days.push($(this).attr("value"));
                }
            });
            $(".cron-box.hour").each(function () {
                if ($(this).prop("checked") === true) {
                    hours.push($(this).attr("value"));
                }
            });

            $("#cron_expr").val(JSON.stringify({days: days, hours: hours}));
        }
    };
});