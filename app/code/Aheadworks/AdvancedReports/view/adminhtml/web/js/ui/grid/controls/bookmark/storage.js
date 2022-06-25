define([
    'jquery',
    'mageUtils',
    'Magento_Ui/js/grid/controls/bookmarks/storage',
    'uiRegistry',
], function ($, utils, storage, registry) {
    'use strict';

    /**
     * Removes ns prefix for path.
     *
     * @param {String} ns
     * @param {String} path
     * @returns {String}
     */
    function removeNs(ns, path) {
        return path.replace(ns + '.', '');
    }

    return storage.extend({
        defaults: {
            ajaxSettings: {
                successActions: []
            }
        },

        /**
         * Sends request to store specified data.
         *
         * @param {String} path - Path by which data should be stored.
         * @param {*} value - Value to be sent.
         */
        set: function (path, value) {
            var property = removeNs(this.namespace, path),
                data = {},
                config;

            utils.nested(data, property, value);

            config = utils.extend({
                url: this.saveUrl,
                data: {
                    data: JSON.stringify(data)
                },
                success: this.onSuccess.bind(this),
            }, this.ajaxSettings);

            $.ajax(config);
        },

        /**
         * On success callback
         */
        onSuccess: function () {
            this.ajaxSettings.successActions.map(
                function (actionConfig) {
                    if (_.isObject(actionConfig)) {
                        this.triggerAction(actionConfig);
                    }
                }, this);
        },

        /**
         * Triggers some methods if defined
         *
         * @param {Object} action - action configuration,
         * must contain actionName and targetName and
         * can contain params
         */
        triggerAction: function (action) {
            var targetName = action.targetName,
                params = action.params || [],
                actionName = action.actionName,
                target;

            target = registry.async(targetName);

            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                target.apply(target, params);
            }
        },
    });
});
