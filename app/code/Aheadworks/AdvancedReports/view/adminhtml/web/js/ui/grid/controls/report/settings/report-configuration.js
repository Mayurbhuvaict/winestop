define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiCollection',
    'Aheadworks_AdvancedReports/js/action/save-report-configuration',
], function ($, _, utils, Collection, saveReportConfiguration) {
    'use strict';

    return Collection.extend({
        defaults: {
            saveUrl: '',
            imports: {
                reportConfiguration: '${ $.provider }:data.report_configuration'
            },
            listens: {
                reportConfiguration: 'onSourceDataUpdate'
            },
            reloadPageOnSave: false
        },

        initialConfigData: {},
        isDataChanged: false,

        /**
         * Save report configuration
         *
         * @returns {Report-Configuration} Chainable
         */
        apply: function () {
            var data = {},
                isSaved = $.Deferred();

            if (!this.isValidForm()) {
                data = {
                    params: {
                        report_configuration: this.reportConfiguration,
                        report_name: this.prepareReportName()
                    },
                    saveUrl: this.saveUrl
                };
                saveReportConfiguration(isSaved, data);
                $.when(isSaved).done(function () {
                    this.isDataChanged = !_.isEqual(this.reportConfiguration, this.initialConfigData);
                    if (this.reloadPageOnSave && this.isDataChanged) {
                        this.reloadPage();
                    }
                }.bind(this));
            }

            return this;
        },

        /**
         * Bookmark on save success action handler
         */
        bookmarkOnSaveSuccessAction: function () {
            if(this.isDataChanged) {
                this.reloadPage();
            }
        },

        /**
         * On source data update handler
         *
         * @param {Object} data
         */
        onSourceDataUpdate: function (data) {
            if (_.isEmpty(this.initialConfigData) && data && !_.isEmpty(data.columns_customization)) {
                this.initialConfigData = utils.copy(data);
            }
        },

        /**
         * Validates each element and returns true, if all elements are valid
         *
         * @returns {Boolean}
         */
        isValidForm: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');

            return this.source.get('params.invalid')
        },

        /**
         * Prepare report name
         *
         * @returns {String}
         */
        prepareReportName: function () {
            return this.ns.replace(/aw_arep_|_grid/g, '');
        },

        /**
         * Reload current page
         */
        reloadPage: function() {
            window.location.reload();
        }
    });
});
