/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["jquery",
    "mage/mage",
    "jquery/ui",
    "Magento_Ui/js/modal/modal",
    "Magento_Ui/js/modal/confirm"], function ($, mage, ui, modal, confirm) {
    "use strict";
    return {
        generate: function (url) {
            confirm({
                title: $.mage.__("Generate data feed"),
                content: $.mage.__("Generate a data feed can take a while. Are you sure you want to generate it now ?"),
                actions: {
                    confirm: function () {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data:{url: url},
                            showLoader: false,
                            success: function () {
                                location.reload();
                            }
                        });
                    },
                    cancel: function () {
                        $(".col-action select.admin__control-select").val("");
                    }
                }
            });
        },
        delete: function (url) {
            confirm({
                title: $.mage.__("Delete data feed"),
                content: $.mage.__("Are you sure you want to delete this feed ?"),
                actions: {
                    confirm: function () {
                        document.location.href = url;
                    },
                    cancel: function () {
                        $(".col-action select.admin__control-select").val("");
                    }
                }
            });
        }
    };
});