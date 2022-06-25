define([
    'underscore',
    'Aheadworks_AdvancedReports/js/ui/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReports/ui/grid/cells/url'
        },

        /**
         * Retrieve row label
         *
         * @param {Array} row
         * @return {String}
         */
        getRowLabel: function(row) {
            return (!_.isUndefined(row['row_label_' + this.index]))
                ? row['row_label_' + this.index]
                : row['row_label'];
        },

        /**
         * Retrieve row url
         *
         * @param {Array} row
         * @return {String}
         */
        getRowUrl: function(row) {
            return (!_.isUndefined(row['row_url_' + this.index]))
                ? row['row_url_' + this.index]
                : row['row_url'];
        }
    });
});
