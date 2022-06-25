define([
    'jquery',
    'underscore',
    'awOscValidationMock'
], function ($, _) {
    'use strict';

    return function (checkoutExpress) {

        return checkoutExpress.extend({
            paypalLoadCheck: false,
            fieldSelector: '.aw-onestep-main input, .aw-onestep-main select, .aw-onestep-main textarea',
            /**
             * @inheritdoc
             */
            initListeners: function() {
                $(this.fieldSelector).on('change', function () {
                    if ($('#co-payment-form #paypal_express').is(':checked')) {
                        this.validate();
                    }
                }.bind(this));

                this._super()
            },

            /**
             *  @inheritdoc
             */
            validate: function () {
                var validMock,
                    styleForError =
                        '<style>' +
                        '.onestepcheckout-index-index div.mage-error[generated] { display: none;} ' +
                        '.onestepcheckout-index-index ._error div.mage-error[generated] { display: block;}' +
                        '</style>';

                this._super();

                if (!this.paypalLoadCheck) {
                    this.paypalLoadCheck = true;
                    validMock = $(this.fieldSelector).awOscValidationMock();
                    validMock.trigger('awOscVMReset', [true]);
                    $('.aw-onestep-main').append(styleForError);
                    $(this.fieldSelector).parents('._error').each(function(){
                        $(this).removeClass('_error');
                    });
                }
            }
        });
    }
});
