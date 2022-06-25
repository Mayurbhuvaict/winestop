define([
    'Magento_Ui/js/lib/spinner',
    'rjsResolver',
    'uiCollection'
], function (loader, resolver, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            listens: {
                '${ $.provider }:reload': 'onBeforeReload',
                '${ $.provider }:reloaded': 'onDataReloaded'
            }
        },

        /**
         * Hides loader
         */
        hideLoader: function () {
            loader.get(this.name).hide();
        },

        /**
         * Shows loader
         */
        showLoader: function () {
            loader.get(this.name).show();
        },

        /**
         * Handler of the data providers' 'reload' event
         */
        onBeforeReload: function () {
            this.showLoader();
        },

        /**
         * Handler of the data providers' 'reloaded' event
         */
        onDataReloaded: function () {
            resolver(this.hideLoader, this);
        }
    });
});
