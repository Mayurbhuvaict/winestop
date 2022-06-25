/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

define(
    [
        'jquery',
        'underscore',
        'ko',
        'uiComponent',
        'Mageplaza_GiftCard/js/model/product',
        'Magento_Ui/js/modal/alert',
        'rjsResolver',
        'jquery/file-uploader',
        'mageplaza/core/owl.carousel',
        'mage/translate'
    ],
    function ($, _, ko, Component, giftCard, alert, resolver) {
        'use strict';

        var config        = window.giftCardInformation,
            configureData = window.configureData,
            templates     = config.template;

        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/product/gallery'
            },
            activeTemplateId: ko.observable(),
            activeTemplate: giftCard.activeTemplate,
            activeImageSrc: giftCard.activeImage,
            uploadedImages: ko.observableArray(),
            previewStyle: ko.observable(),
            previewWidth: ko.observable('80%'),
            from: giftCard.from,
            to: giftCard.to,
            message: giftCard.message,
            amount: giftCard.activeAmount,
            uploadUrl: config.information.fileUploadUrl,
            uploadTooltip: config.information.uploadTooltip,

            /**
             * Init component
             */
            initialize: function () {
                this._super();

                this.initCardFields();
                this.initConfigureData();

                return this;
            },

            initSlide: function () {
                var container = $('.giftcard-template-choose-images');

                $('.owl-stage-outer').remove();
                if (container.children('.image.item-template').length) {
                    container.children('.owl-splatage-outer').remove();
                }

                container.trigger('destroy.owl.carousel');
                container.find('.owl-stage-outer').children().unwrap();

                if (!container.hasClass('owl-carousel owl-theme owl-center')) {
                    container.addClass('owl-carousel owl-theme owl-center');
                }

                container.owlCarousel({
                    loop: false,
                    margin: 10,
                    items: 5,
                    center: true,
                    dots: false,
                    nav: true,
                    navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
                });
            },

            initEvent: function () {
                var self = this;

                $('#giftcard-template-select').on('change', function () {
                    self.activeTemplateId(Number(this.value));
                });

                $('#giftcard-template-container-loader').remove();
            },

            initObservable: function () {
                var self = this;

                this._super().observe({
                    fields: [],
                    cardStyle: '',
                    image: ''
                });

                this.activeTemplateId.subscribe(function (templateId) {
                    var activeTemplate = _.find(self.templates, function (template) {
                        return Number(template.id) === templateId;
                    });

                    self.activeTemplate(activeTemplate);
                    if (self.templates.length > 5) {
                        self.changeDesign(activeTemplate);
                    }
                    if (activeTemplate.images.length) {
                        self.activeImageSrc(activeTemplate.images[0].file);
                        self.image(activeTemplate.images[0].src);
                    }

                    self.fields($.map(activeTemplate.design, function (value, index) {
                        switch (index){
                            case 'from':
                                value.value = ko.computed(function () {
                                    var from = typeof self.from() !== 'undefined' ? self.from() : '';

                                    return $.mage.__('From: ') + from;
                                });
                                break;
                            case 'to':
                                value.value = ko.computed(function () {
                                    var to = typeof self.to() !== 'undefined' ? self.to() : '';

                                    return $.mage.__('To: ') + to;
                                });
                                break;
                            case 'message':
                                value.value = self.message;
                                break;
                            case 'value':
                                value.value = ko.computed(function () {
                                    if (typeof self.amount() !== 'undefined') {
                                        return self.amount().amountFormatted;
                                    }
                                    return 0;
                                });
                                break;
                            case 'image':
                                value.src = self.image;
                                break;
                        }

                        return [value];
                    }));

                    if (activeTemplate.card.css['font-family'] === undefined) {
                        activeTemplate.card.css['font-family'] = activeTemplate.font;
                    }
                    $('.preview-giftcard').removeAttr('style');
                    self.cardStyle(activeTemplate.card.css);
                    self.calculateScale();
                });

                $(window).resize(function () {
                    self.calculateScale();
                });

                return this;
            },

            /**
             * computed variable for show gift card template
             */
            initCardFields: function () {
                // Convert templates to array
                this.templates = $.map(templates, function (value) {
                    return [value];
                });

                if (!this.templates.length) {
                    return;
                }

                this.activeTemplateId(this.templates[0].id);
                resolver(this.initUploadFile.bind(this));
            },

            /**
             * Init saved data
             * @returns {exports}
             */
            initConfigureData: function () {
                var templateId, template, imageSrc, data, image;

                if (configureData.hasOwnProperty('template')) {
                    return;
                }

                templateId = Number(configureData.template);
                template   = _.find(this.templates, function (templateTmp) {
                    return Number(templateTmp.id) === templateId;
                });

                if (typeof template === 'undefined') {
                    return;
                }

                this.changeDesign(template);

                if (!configureData.hasOwnProperty('image')) {
                    return;
                }

                imageSrc = configureData.image;

                if (imageSrc.indexOf('.tmp') !== -1 && configureData.hasOwnProperty('imageSrc')) {
                    data = {
                        src: configureData.imageSrc,
                        file: imageSrc
                    };

                    this.uploadedImages.push(data);
                    this.changeImages(data);
                } else if (template.images.length) {
                    image = _.find(template.images, function (imageTmp) {
                        return imageTmp.file === imageSrc;
                    });

                    if (typeof image !== 'undefined') {
                        this.changeImages(image);
                    }
                }
            },

            /**
             * Init upload file button
             */
            initUploadFile: function () {
                var self                 = this,
                    uploader             = $('#image-uploader input[type=file]'),
                    uploadElementSpinner = $('#image-uploader-spinner');

                uploader.fileupload({
                    process: [{
                        action: 'load',
                        fileTypes: /^image\/(gif|jpeg|png)$/,
                        maxFileSize: 1048576 //1MB
                    }],
                    dataType: 'json',
                    sequentialUploads: true,
                    add: function (e, data) {
                        uploadElementSpinner.show();
                        $(this).fileupload('process', data).done(function () {
                            data.submit();
                        });
                    },
                    done: function (e, data) {
                        if (data.result && !data.result.error) {
                            data.result.src = data.result.url;
                            self.uploadedImages.push(data.result);
                            self.changeImages(data.result);
                        } else {
                            alert({content: $.mage.__('We don\'t recognize or support this file extension type.')});
                        }
                        uploadElementSpinner.hide();
                    }
                });
            },

            /**
             * @param template
             */
            changeDesign: function (template) {
                if (this.activeTemplateId() === template.id) {
                    return;
                }

                resolver(this.initUploadFile.bind(this));

                this.activeTemplateId(Number(template.id));

                this.initSlide();

                if (this.activeTemplate().images.length) {
                    this.activeImageSrc(this.activeTemplate().images[0].file);
                    this.image(this.activeTemplate().images[0].src);
                } else {
                    this.activeImageSrc('');
                }
            },

            /**
             *
             * @param image
             */
            changeImages: function (image) {
                this.activeImageSrc(image.file);
                this.image(image.src);
            },

            /**
             * Calculate scale for template image show on product image area
             * @returns {exports}
             */
            calculateScale: function () {
                var card     = this.cardStyle(),
                    width    = parseInt(card.width, 10),
                    height   = parseInt(card.height, 10),
                    maxWidth = Number($('#giftcard-template-container').width()) * 0.8,
                    rate     = Math.min(maxWidth / width, 350 / height, 1);

                this.previewStyle({
                    transform: 'scale(' + rate + ')',
                    height: Math.max(rate * height, 350) + 'px'
                });

                this.previewWidth(rate * width + 'px');

                return this;
            },

            isImageField: function (key) {
                return $.inArray(key, ['image', 'logo', 'barcode']) !== -1;
            }
        });
    }
);

