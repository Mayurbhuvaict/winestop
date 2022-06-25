/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'mage/template',
    'text!Mageplaza_GiftCard/template/form/design/field.html',
    'jquery/ui',
    'mage/validation'
], function ($, mageTemplate, fieldDesignTemplate) {
    'use strict';

    $.widget('mageplaza.giftCardDesign', {
        options: {
            fieldPrefix: '#design-field-',
            draggableElement: '.draggable',
            dropzoneElement: '.dropzone'
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            var designTab = $('#giftcard_template_edit_tabs_design_content');

            designTab.show();
            this.initFields();
            designTab.hide();

            this.initDraggable();
            this.initPopupEdit();
            this.initColor();
            this.initTemplateLoad();
        },

        /**
         * Reinit field when load from exist template
         */
        initTemplateLoad: function () {
            var self           = this,
                buttonSubmit   = $('#template-load'),
                templateSelect = $('#template_select');

            if ($.isEmptyObject(this.options.existDesign)) {
                return;
            }

            buttonSubmit.on('click', function () {
                if (!templateSelect.val()) {
                    return;
                }

                $.each(self.options.initFields, function (key) {
                    var element = $(self.options.fieldPrefix + key);

                    element.css(self.originPosition[key]);
                    self.updateFields(key);
                });

                self.options.fields = $.extend({}, JSON.parse(self.options.existDesign[templateSelect.val()].design));
                $.each(self.options.fields, function (id, field) {
                    self.updateFields(id, field, true);
                    if (id === 'giftcard') {
                        self.recalculateGiftCardPosition(field);
                    }
                });
            });
        },

        /**
         * Init color from Information tab
         */
        initColor: function (triggerOnly) {
            var options            = this.options.fields.giftcard,
                title              = $('#title'),
                note               = $('#note'),
                font               = $('#font_family'),
                backgroundImageEl  = $('#background_image'),
                backgroundImageImg = $('#background_image_image'),
                giftCard           = $('#design-field-giftcard'),
                fieldContainer     = $('.giftcard-drag-drop-left');

            if (typeof triggerOnly === 'undefined') {
                title.on('change', function () {
                    var titleEl = $('#design-field-title');

                    titleEl.data('sample-content', $(this).val());
                    if (titleEl.hasClass('drag-in')) {
                        titleEl.find('.sample-content').text($(this).val());
                    }
                });
                note.on('change', function () {
                    var noteEl = $('#design-field-note');

                    noteEl.data('sample-content', $(this).val());
                    if (noteEl.hasClass('drag-in')) {
                        noteEl.find('.sample-content').text($(this).val());
                    }
                });
                font.on('change', function () {
                    if ($(this).val() && (typeof options['css'] === 'undefined'
                        || typeof options['css'] !== 'undefined'
                        && typeof options['css']['color'] === 'undefined')) {
                        fieldContainer.css('font-family', '"' + $(this).val() + '"');
                    }
                });
                backgroundImageEl.on('change', function () {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        if (typeof options['css'] === 'undefined'
                            || typeof options['css'] !== 'undefined'
                            && typeof options['css']['background'] === 'undefined') {
                            giftCard.css('background', 'url(' + e.target.result + ') no-repeat top left');
                        }
                    };
                    if (typeof $(this)[0].files[0] !== 'undefined') {
                        reader.readAsDataURL($(this)[0].files[0]);
                    } else if (backgroundImageImg.length) {
                        if (typeof options['css'] === 'undefined'
                            || typeof options['css'] !== 'undefined'
                            && typeof options['css']['background'] === 'undefined') {
                            giftCard.css(
                                'background', 'url(' + backgroundImageImg.attr('src') + ') no-repeat top left'
                            );
                        }
                    }
                });
            }

            if (title.val()) {
                title.trigger('change');
            }
            if (note.val()) {
                note.trigger('change');
            }
            font.trigger('change');
            backgroundImageEl.trigger('change');
        },

        /**
         * Init fields
         */
        initFields: function () {
            var self             = this,
                dropzonePosition = $(this.options.dropzoneElement).position();

            /** Init Dropzone Position */
            this.cardPosition = {
                top: self.num(dropzonePosition.top),
                left: self.num(dropzonePosition.left),
                right: self.num(dropzonePosition.left) + this.options.fields.giftcard.width,
                bottom: self.num(dropzonePosition.top) + this.options.fields.giftcard.height
            };
            this.inputField     = $('#giftcard-design-input');
            this.originPosition = {};
            $.each(this.options.initFields, function (id) {
                var element  = $(self.options.fieldPrefix + id),
                    position = element.position();

                self.originPosition[id] = {
                    top: position.top + 'px',
                    left: position.left + 'px',
                    width: element.css('width'),
                    height: element.css('height')
                };
            });

            /** Init field position */
            $.each(this.options.fields, function (id, field) {
                self.updateFields(id, field, true);
            });
        },

        /**
         * Init Draggable for fields
         */
        initDraggable: function () {
            var self = this;

            $(this.options.draggableElement)
            .draggable({
                snap: "#design-field-giftcard",
                snapMode: "inner",
                snapTolerance: 5,
                stack: '.draggable',
                containment: ".giftcard-template-design",
                stop: function (event, ui) {
                    var key  = $(this).attr('data-id'),
                        top  = ui.position.top,
                        left = ui.position.left;

                    if (self.checkZoneAndResetFields(key, ui)) {
                        self.updateFields(key, {
                            top: top - self.cardPosition.top,
                            left: left - self.cardPosition.left
                        });
                    }
                }
            })
            .resizable({
                maxHeight: self.options.fields.giftcard.height,
                minHeight: 20,
                maxWidth: self.options.fields.giftcard.width,
                minWidth: 50,
                handles: 'all',
                stop: function (event, ui) {
                    var key = $(this).data('id');

                    if (self.checkZoneAndResetFields(key, ui)) {
                        self.updateFields(key, {
                            top: ui.position.top - self.cardPosition.top,
                            left: ui.position.left - self.cardPosition.left,
                            width: ui.size.width,
                            height: ui.size.height
                        });
                    }
                }
            });
        },

        /**
         * Popup edit field attribute
         */
        initPopupEdit: function () {
            var self  = this,
                modal = {};

            $.each(this.element.find('.design-field-edit'), function () {
                var key     = $(this).data('id'),
                    element = $(self.options.fieldPrefix + key),
                    label   = {
                        key: key,
                        size_label: key === 'giftcard' ? $.mage.__('Gift Card Size') : $.mage.__('Field Size'),
                        width_label: $.mage.__('Width'),
                        height_label: $.mage.__('Height'),
                        position_label: $.mage.__('Field Position'),
                        top_label: $.mage.__('Top'),
                        left_label: $.mage.__('Left'),
                        css_label: $.mage.__('Custom Css')
                    };

                $(this).on('click', function () {
                    var data = $.extend({}, label, self.options.fields[key]), css, title, form, dataUpdate;

                    if (typeof data.css !== 'undefined') {
                        css = '';
                        $.each(data.css, function (att, value) {
                            css += (css !== '' ? '; ' : '') + att + ': ' + value;
                        });
                        data.css = css;
                    }

                    if (typeof modal[key] === 'undefined') {
                        title = key === 'giftcard'
                            ? element.data('label')
                            : $.mage.__('Edit Field "') + element.data('label') + '"';

                        modal[key] = $('<div></div>')
                        .html(mageTemplate(fieldDesignTemplate, data))
                        .modal({
                            title: title,
                            modalClass: '_image-box',
                            buttons: [{
                                text: $.mage.__('Update'),
                                click: function () {
                                    form       = $('#giftcard-design-field-' + key);
                                    dataUpdate = {};

                                    form.validation();
                                    if (!form.valid()) {
                                        return;
                                    }

                                    $.each(form.serializeArray(), function (arrKey, field) {
                                        var cssObj, cssAtt;

                                        if (field.name === 'css') {
                                            if (!field.value) {
                                                return;
                                            }
                                            cssObj = {};
                                            $.each(field.value.split(";"), function (index1, value1) {
                                                if (!value1) {
                                                    return;
                                                }
                                                cssAtt = value1.split(":");
                                                if (typeof cssAtt[0] !== 'undefined' && cssAtt[0] &&
                                                    typeof cssAtt[1] !== 'undefined' && cssAtt[1] &&
                                                    cssAtt[0].trim() !== 'margin'
                                                ) {
                                                    cssObj[cssAtt[0].trim()] = cssAtt[1].trim();
                                                }
                                            });
                                            if (!$.isEmptyObject(cssObj)) {
                                                dataUpdate[field.name] = cssObj;
                                            }
                                            return;
                                        }
                                        dataUpdate[field.name] = self.num(field.value);
                                    });
                                    self.updateFields(key, dataUpdate, true);
                                    if (key === 'giftcard') {
                                        self.recalculateGiftCardPosition(dataUpdate);
                                    }
                                    this.closeModal();
                                }
                            }]
                        });
                    } else {
                        form = $('#giftcard-design-field-' + key);

                        $.each(['width', 'height', 'top', 'left', 'css'], function (index, el) {
                            var inputEl = form.find('#design-field-' + key + '-' + el);

                            if (inputEl.length) {
                                inputEl.val(data[el]);
                            }
                        });
                    }

                    modal[key].trigger('openModal');
                });
            });
        },

        recalculateGiftCardPosition: function (dataUpdate) {
            var self = this, dropzonePosition;

            $(self.options.draggableElement).resizable('option', {
                maxHeight: dataUpdate.height,
                maxWidth: dataUpdate.width
            });
            dropzonePosition  = $(self.options.dropzoneElement).position();
            self.cardPosition = {
                top: self.num(dropzonePosition.top),
                left: self.num(dropzonePosition.left),
                right: self.num(dropzonePosition.left) + self.num(dataUpdate.width),
                bottom: self.num(dropzonePosition.top) + self.num(dataUpdate.height)
            };
        },

        /**
         * Update value to input hidden
         * Update field css if edited from popup
         *
         * @param fieldId
         * @param param
         * @param updateCss
         */
        updateFields: function (fieldId, param, updateCss) {
            var self    = this,
                element = $(this.options.fieldPrefix + fieldId);

            if (typeof param === 'undefined') {
                if (typeof this.options.fields[fieldId] !== 'undefined') {
                    $.each(this.options.fields[fieldId], function (key, value) {
                        if (key === 'css') {
                            $.each(value, function (attr) {
                                element.find('.label-content').css(attr, '');
                            });
                        }
                    });
                    if (element.attr('data-sample-content')) {
                        element.css('background-color', '#29e');
                        element.find('.sample-content').text(element.data('label'));
                    }
                    element.removeClass('drag-in');
                }

                delete this.options.fields[fieldId];
            } else {
                if (typeof this.options.fields[fieldId] === 'undefined') {
                    this.options.fields[fieldId] = {
                        width: self.num(element.css('width')),
                        height: self.num(element.css('height')),
                        top: self.num(element.position().top) - self.cardPosition.top,
                        left: self.num(element.position().left) - self.cardPosition.left
                    };

                    if (typeof this.options.initFields[fieldId].css !== 'undefined') {
                        this.options.fields[fieldId].css = this.options.initFields[fieldId].css;
                        self.updateContentCss(element, 'css', this.options.initFields[fieldId].css);
                    }
                }

                if (element.attr('data-sample-content')) {
                    element.css('background-color', 'transparent');
                    element.find('.sample-content').html(element.attr('data-sample-content'));
                }
                element.addClass('drag-in');

                $.each(param, function (key, value) {
                    var addPosition, realValue;

                    value = key !== 'css' ? self.num(value) : value;
                    if (typeof updateCss !== 'undefined') {
                        if (key === 'css') {
                            self.updateContentCss(element, key, value);
                        } else {
                            addPosition = key === 'top'
                                ? self.cardPosition.top
                                : key === 'left' ? self.cardPosition.left : 0;

                            realValue = self.num(value) + addPosition;

                            element.css(key, realValue + 'px');
                        }
                    }
                    self.options.fields[fieldId][key] = value;
                });
            }

            this.inputField.val(JSON.stringify(this.options.fields));
        },

        /**
         * Add css for fields
         *
         * @param element
         * @param key
         * @param value
         */
        updateContentCss: function (element, key, value) {
            var content = element.find('.label-content'),
                origCss = typeof this.options.fields[element.data('id')][key] !== 'undefined'
                    ? this.options.fields[element.data('id')][key]
                    : {};

            content = content.length ? content : element;

            $.each(origCss, function (attr) {
                content.css(attr, '');
            });
            if (typeof value !== 'undefined') {
                content.css(value);
            }
        },

        /**
         * Check field is dragged out of the card zone and revert/reset it
         *
         * @param key
         * @param ui
         * @returns {boolean}
         */
        checkZoneAndResetFields: function (key, ui) {
            var element = $(this.options.fieldPrefix + key),
                width   = this.num(element.css('width')),
                height  = this.num(element.css('height')),
                top     = this.num(ui.position.top),
                left    = this.num(ui.position.left),
                right   = left + width,
                bottom  = top + height,
                delta   = Math.min(
                    top - this.cardPosition.top,
                    this.cardPosition.bottom - bottom,
                    left - this.cardPosition.left,
                    this.cardPosition.right - right
                );

            var revertDelta = Math.min(Math.min(width, height) / 2, 50), originPosition;

            if (delta >= 0) {
                return true;
            } else if (delta >= -revertDelta) {
                originPosition = ui.originalPosition;

                if (typeof ui.originalSize !== 'undefined') {
                    $.extend(originPosition, ui.originalSize);
                }
                element.animate(originPosition, 500);
            } else {
                element.animate(this.originPosition[key], 500);
                this.updateFields(key);
            }

            return false;
        },

        /**
         * Format number
         * @param v
         * @returns {Number|number}
         */
        num: function (v) {
            return parseInt(v, 10) || 0;
        }
    });

    return $.mageplaza.giftCardDesign;
});

