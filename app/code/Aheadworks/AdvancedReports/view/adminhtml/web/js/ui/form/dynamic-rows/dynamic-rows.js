define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (_, dynamicRows) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            listens: {
                '${ $.provider }:reloaded': 'onDataReloaded'
            }
        },
        _appliedChanged: false,

        /**
         * Handler for the data provider reloaded event
         */
        onDataReloaded: function () {
            if (!this._appliedChanged) {
                this._appliedChanged = true;
                this.reload();
            }
        }
    });
});
