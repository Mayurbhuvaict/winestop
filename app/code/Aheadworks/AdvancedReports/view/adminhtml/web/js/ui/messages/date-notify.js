define([
    'jquery',
    'uiComponent',
    'moment'
], function ($, Component, moment) {
    'use strict';

    return Component.extend({
        defaults: {
            name: 'dateNotify',
            template: 'Aheadworks_AdvancedReports/ui/messages/date-notify',
            date: ''
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe(['date']);

            return this;
        },

        /**
         * Get date
         *
         * @returns {string|null}
         */
        getDate: function () {
            if (this.date() !== '') {
                return moment(this.date()).format('MMM D, YYYY');
            }

            return null;
        },

        /**
         * Set date
         *
         * @param date
         * @return this
         */
        setDate: function (date) {
            this.date(date);

            return this;
        }
    });
});
