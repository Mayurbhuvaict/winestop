define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'jquery/jquery.cookie'
], function ($, ui, modal) {
    'use strict';

    $.widget('mirasvit.templatePreview', {
        options: {
            url: null,
        },

        _create: function () {
            this.element
                .off('click.button')
                .on('click.button', $.proxy(this.preview, this));

            this._super();
        },

        preview: function () {
            var self = this;

            var modal = $('<div/>').modal({
                type: 'slide',
                title: $.mage.__('Template Preview'),
                modalClass: 'preview-aside',
                closeOnEscape: true,
                opened: function () {
                    $('body').trigger('processStart');

                    $(this).html(self.getIframe());

                    self.getForm().submit();

                    $('iframe', this).load(function () {
                        $('body').trigger('processStop');
                    });
                },
                closed: function () {
                    $('.preview-aside').remove();
                },

                buttons: [
                    {
                        text: $.mage.__('Reload'),
                        click: function (e) {
                            self.getForm().submit();
                        }
                    }
                ]
            });

            modal.modal('openModal');
        },

        getIframe: function () {
            return $('<iframe>')
                .attr('name', 'preview_iframe');
        },

        getForm: function () {
            var actionUrl  = this.options.url;
            var cookieName = '';


            if (actionUrl.indexOf('template_id') > 0) {
                var templateId = actionUrl.match(/template_id\/(\w+)/);
                templateId = templateId[1];
                cookieName = "template_" + templateId + "_preview_ids";
            }

            $("[target=preview_iframe]").remove();
            var previewIds = $.cookie(cookieName);
            var $form = $('<form/>')
                .attr('action', this.options.url)
                .attr('method', 'post')
                .attr('target', 'preview_iframe')
                .css('display', 'none');

            var fieldsetData = $('fieldset').serialize();
            fieldsetData = fieldsetData.split('&');

            var fieldsetFormattedData = [];

            fieldsetData.forEach(function (data) {
                data = data.split('=');
                fieldsetFormattedData[data[0]] = data[1];
            });

            var registry   = require('uiRegistry');
            var fieldNames = [
                'rule_type',
                'is_active',
                'description_position',
                'store_ids',
                'stop_rules_processing',
                'apply_for_child_categories',
                'apply_for_homepage'
            ];

            var additionalData = [];
            var component      = null;

            // get data that can't be retrieved with $('fieldset').serialize()
            fieldNames.forEach(function (name) {
                component = registry.get('index = ' + name);
                var value = component.value();

                if (Array.isArray(value)) {
                    value.join(',');
                }

                additionalData[name] = encodeURI(value);
            });

            // add additional data to form data
            fieldNames.forEach(function (value) {
                fieldsetFormattedData[value] = additionalData[value];
            });

            // prepare query string
            var dataString = Object.keys(fieldsetFormattedData).map(function(key) {
                return key + '=' + fieldsetFormattedData[key]
            }).join('&');

            $form.append($('<textarea>')
                .attr('name', 'data')
                .text(dataString));

            $form.append($('<input>')
                .attr('name', 'preview_param')
                .val(previewIds));

            $('body').append($form);

            return $form;
        }
    });

    return $.mirasvit.templatePreview;
});
