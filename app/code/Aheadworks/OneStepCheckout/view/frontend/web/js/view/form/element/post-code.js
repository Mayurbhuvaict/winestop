define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
], function (_, registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            countryFieldIncludedRow: '',
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            this._super();

            /**
             * equalityComparer function
             *
             * @returns boolean.
             */
            this.value.equalityComparer = function (oldValue, newValue) {
                return !oldValue && !newValue || oldValue === newValue;
            };

            return this;
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            //Start small fix to make this field available under different parent
            var elem = this.countryFieldIncludedRow ? this.countryFieldIncludedRow : this.parentName,
                country = registry.get(elem + '.' + 'country_id'),
                options = country.indexedOptions,
                option = null;
            // End fix

            if (!value) {
                return;
            }

            option = options[value];

            if (!option) {
                return;
            }

            if (option['is_zipcode_optional']) {
                this.error(false);
                this.validation = _.omit(this.validation, 'required-entry');
            } else {
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
        }
    });
});
