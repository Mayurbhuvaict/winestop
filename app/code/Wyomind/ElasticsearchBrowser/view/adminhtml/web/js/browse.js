define(["jquery", "jquery/ui", "Magento_Ui/js/modal/modal", "elasticsearchbrowser_jsonview"], function ($) {
    "use strict";
    return {
        raw: function (url) {
            var raw = $('#raw');
            raw.modal({
                "type": "slide",
                "title": "Raw data",
                "modalClass": "mage-new-category-dialog form-inline",
                buttons: []
            });

            raw.html("");
            raw.modal("openModal");

            $.ajax({
                url: url,
                data: {},
                type: "GET",
                showLoader: true,
                success: function (data) {
                    raw.html(JSON.stringify(data));
                    raw.JSONView(data);
                },
                error: function (data) {
                    raw.html("<hr style='border:1px solid #e3e3e3'/><br/>" + data.responseText);
                }
            });
        }
    };
}); 