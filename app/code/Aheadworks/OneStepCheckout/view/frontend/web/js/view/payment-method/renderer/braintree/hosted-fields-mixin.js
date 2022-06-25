define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        return function (renderer) {
            return renderer.extend({

                /**
                 * @returns {exports.initialize}
                 */
                initialize: function () {
                    var parentSelector = '#payment_form_braintree',
                        imgSelector = '.field.number .braintree-credit-card-selected';

                    $.async(parentSelector, function () {
                        if ($(parentSelector + ' ' + imgSelector).length) {
                            $(parentSelector).addClass('payment_form_gene-braintree');
                        }
                    });

                    return this._super();
                }
            });
        }
    }
);
