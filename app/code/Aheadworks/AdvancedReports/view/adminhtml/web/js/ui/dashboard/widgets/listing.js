define([
    'uiCollection'
], function (Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/dashboard/widgets/listing'
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Listing} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        }
    });
});
