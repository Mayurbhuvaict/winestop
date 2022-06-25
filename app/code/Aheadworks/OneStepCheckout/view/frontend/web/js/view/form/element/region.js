define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/default-post-code-resolver'
], function (_, registry, Select, defaultPostCodeResolver) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            countryFieldIncludedRow: '',
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            //Start small fix to make this field available under different parent
            var elem = this.countryFieldIncludedRow ? this.countryFieldIncludedRow : this.parentName,
                country = registry.get(elem + '.' + 'country_id'),
                // End fix
                options = country.indexedOptions,
                isRegionRequired,
                option;

            if (!value) {
                return;
            }
            option = options[value];

            if (typeof option === 'undefined') {
                return;
            }

            defaultPostCodeResolver.setUseDefaultPostCode(!option['is_zipcode_optional']);

            if (this.skipValidation) {
                this.validation['required-entry'] = false;
                this.required(false);
            } else {
                if (option && !option['is_region_required']) {
                    this.error(false);
                    this.validation = _.omit(this.validation, 'required-entry');
                    registry.get(this.customName, function (input) {
                        input.validation['required-entry'] = false;
                        input.required(false);
                    });
                } else {
                    this.validation['required-entry'] = true;
                }

                if (option && !this.options().length) {
                    registry.get(this.customName, function (input) {
                        isRegionRequired = !!option['is_region_required'];
                        input.validation['required-entry'] = isRegionRequired;
                        input.validation['validate-not-number-first'] = true;
                        input.required(isRegionRequired);
                    });
                }

                this.required(!!option['is_region_required']);
            }
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function (value, field) {
            var superFn = this._super,
                //Start small fix to make this field available under different parent
                elem = this.countryFieldIncludedRow ? this.countryFieldIncludedRow : this.parentName;

            registry.get(elem + '.' + 'country_id', function (country) {
            // End fix
                var option = country.indexedOptions[value];

                superFn.call(this, value, field);

                if (option && option['is_region_visible'] === false) {
                    // hide select and corresponding text input field if region must not be shown for selected country
                    this.setVisible(false);

                    if (this.customEntry) {// eslint-disable-line max-depth
                        this.toggleInput(false);
                    }
                }
            }.bind(this));
        }
    });
});

