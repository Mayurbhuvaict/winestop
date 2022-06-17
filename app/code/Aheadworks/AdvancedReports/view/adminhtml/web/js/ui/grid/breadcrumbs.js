define([
    'uiElement'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/grid/breadcrumbs'
        },

        /**
         * Checks if crumbs has data
         *
         * @returns {Boolean}
         */
        hasCrumbs: function () {
            return !!this.crumbs && !!this.crumbs.length;
        },

        /**
         * Check if crumb is url
         *
         * @param {Object} crumb
         * @return {Boolean}
         */
        isUrl: function (crumb) {
            return crumb.url && !crumb.last;
        }
    });
});
