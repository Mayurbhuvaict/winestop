define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/listing'
], function ($, _, Listing) {
    'use strict';

    return Listing.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/listing',
            imports: {
                totals: '${ $.provider }:data.totals'
            },
            dndConfig: {
                component: 'Aheadworks_AdvancedReports/js/ui/grid/dnd'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()._initHistoryListener();

            return this;
        },

        /**
         * Initializes observable properties
         *
         * @returns {Listing} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    totals: []
                });

            return this;
        },

        /**
         * Check if display totals
         *
         * @return {boolean}
         */
        isDisplayTotals: function () {
            return (!_.isUndefined(this.displayTotals) && this.displayTotals)
                || _.isUndefined(this.displayTotals);
        },

        /**
         * Initialize history listener
         */
        _initHistoryListener: function () {
            var state = {};

            if (_.isEmpty(window.history.state)) {
                state.title =  window.document.title;
                state.url = location.href;
                window.history.replaceState(state, state.title, state.url);
            }

            window.history.scrollRestoration = 'manual';

            $(window).off('popstate');
            $(window).on('popstate', function(event) {
                var originalEvent = event.originalEvent;

                if (!_.isEmpty(originalEvent.state)) {
                    window.location.replace(originalEvent.state.url);
                    $.Deferred().resolve();
                }
            }.bind(this));
        }
    });
});
