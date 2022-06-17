define([
    'underscore',
    'mageUtils',
    'Aheadworks_AdvancedReports/js/ui/toolbar/dropdown'
], function (_, utils, Dropdown) {
    'use strict';

    return Dropdown.extend({
        defaults: {
            applied: '',
            allowUpdateUrl: false,
            statefull: {
                applied: true
            },
            listens: {
                applied: 'updateActive'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super().updateActive();

            return this;
        },

        /**
         * Resets current value to the last applied value state
         *
         * @returns {Dropdown} Chainable
         */
        updateActive: function () {
            if (_.isEmpty(this.applied)) {
                this.currentValue = this.getDefaultOption();
                return this;
            }
            this.set('currentValue', utils.copy(this.applied));

            return this;
        }
    });
});
