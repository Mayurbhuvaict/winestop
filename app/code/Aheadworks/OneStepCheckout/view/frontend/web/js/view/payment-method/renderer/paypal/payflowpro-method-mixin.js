define(
    [
        'jquery',
        'Magento_Captcha/js/model/captchaList'
    ],
    function ($, captchaList) {
        'use strict';

        return function (renderer) {
            return renderer.extend({
                defaults: {
                    paypalPayflowTemplate: 'Magento_PaypalCaptcha/payment/payflowpro-form'
                },

                /**
                 * @returns {exports.initialize}
                 */
                initialize: function () {
                    var component = this._super();

                    component.template = this.paypalPayflowTemplate;
                    $(window).off('clearTimeout').on('clearTimeout', this.clearTimeout.bind(this));

                    return component;
                },

                /**
                 * Overrides default window.clearTimeout() to catch errors from iframe and reload Captcha.
                 */
                clearTimeout: function () {
                    var captcha = captchaList.getCaptchaByFormId(this.formId);

                    if (captcha !== null) {
                        captcha.refresh();
                    }
                    clearTimeout();
                }
            });
        }
    }
);