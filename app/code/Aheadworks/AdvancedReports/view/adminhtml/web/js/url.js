define([
    'jquery',
    'Aheadworks_AdvancedReports/js/url/processor'
], function ($, processor) {
    'use strict';

    return {
        currentUrl: window.location.href,
        filterRequestParams: [],

        /**
         * Set current url
         *
         * @param {String} url
         */
        setCurrentUrl: function (url) {
            this.currentUrl = url;
        },

        /**
         * Get current url
         *
         * @returns {String}
         */
        getCurrentUrl: function () {
            return this.currentUrl;
        },

        /**
         * Get submit url
         *
         * @param {Array} filterValue
         * @returns {String}
         */
        getSubmitUrl: function (filterValue) {
            return processor.updateParams(this.currentUrl, processor.prepareFilterValue(filterValue));
        }
    };
});
