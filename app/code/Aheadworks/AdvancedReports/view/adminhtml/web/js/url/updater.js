define([
    'jquery',
    'Aheadworks_AdvancedReports/js/url'
], function ($, url) {
    'use strict';

    return {
        /**
         * Update url
         *
         * @param {String} windowUrl
         */
        updateUrl: function (windowUrl) {
            this._setCurrentUrl(windowUrl);
        },

        /**
         * Set current url
         *
         * @param {String} windowUrl
         */
        _setCurrentUrl: function (windowUrl) {
            var state = {};

            url.setCurrentUrl(windowUrl);
            if (typeof(window.history.pushState) == 'function') {
                state.title =  window.document.title;
                state.url = url.getCurrentUrl();
                window.history.pushState(state, state.title, state.url);
            } else {
                window.location.hash = '#!' + url.getCurrentUrl();
            }
        }
    };
});
