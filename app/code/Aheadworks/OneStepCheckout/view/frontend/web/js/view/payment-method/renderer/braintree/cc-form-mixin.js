define(
    [
        'jquery',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converter/braintree-hosted-fields'
    ],
    function ($, flLabelConverter) {
        'use strict';

        return function (renderer) {
            return renderer.extend({

                /**
                 * @inheritdoc
                 */
                getFieldsConfiguration: function () {
                    var fields = this._super();

                    delete fields.expirationMonth.placeholder;
                    delete fields.expirationYear.placeholder;

                    return fields;
                },

                /**
                 * @inheritdoc
                 */
                initFormValidationEvents: function (hostedFieldsInstance) {
                    this._super(hostedFieldsInstance);

                    hostedFieldsInstance.on('focus', this.updateFloatLabelState);
                    hostedFieldsInstance.on('blur', this.updateFloatLabelState);
                },

                /**
                 * Update float label state
                 *
                 * @param {Object} event
                 */
                updateFloatLabelState: function (event) {
                    var expDateContainersSelector = [
                            flLabelConverter.expMonthSelector,
                            flLabelConverter.expYearSelector
                        ].join(', '),
                        targetFieldEventData = event.fields[event.emittedBy],
                        container = $(targetFieldEventData.container),
                        flFieldElement = container.closest('div.field'),
                        field = flFieldElement.find(expDateContainersSelector).length > 0
                            ? container.closest(expDateContainersSelector)
                            : flFieldElement;

                    flFieldElement.awOscBraintreeHostedFloatLabel(
                        'triggerFieldEvent',
                        field,
                        targetFieldEventData.isFocused,
                        targetFieldEventData.isEmpty
                    );
                }
            });
        }
    }
);
