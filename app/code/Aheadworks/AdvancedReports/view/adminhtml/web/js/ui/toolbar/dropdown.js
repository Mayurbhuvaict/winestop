define([
    'underscore',
    'mageUtils',
    'Aheadworks_AdvancedReports/js/url',
    'Aheadworks_AdvancedReports/js/url/updater',
    'uiElement'
], function (_, utils, url, urlUpdater, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/toolbar/dropdown',
            isAjax: true,
            allowUpdateUrl: true,
            listens: {
                currentValue: 'initCurrentValue',
                applied: 'onAppliedChanged',
                '${ $.provider }:reloaded': 'onDataReloaded'
            },
            exports: {
                applied: '${ $.provider }:params.${ $.filterScope }'
            }
        },
        _appliedChanged: false,

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super().initCurrentValue();

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .track([
                    'currentValue',
                    'options'
                ]);

            return this;
        },

        /**
         * Initialize current value
         *
         * @return {Dropdown} Chainable
         */
        initCurrentValue: function () {
            if (_.isUndefined(this.currentValue) || _.isNull(this.currentValue)) {
                this.currentValue = this.getDefaultOption();
            }

            return this;
        },

        /**
         * Set new value and apply
         *
         * @param {String} value
         * @returns {Boolean}
         */
        apply: function (value) {
            if (!this.isAjax) {
                return true;
            }

            this.currentValue = value;
            this.set('applied', this.currentValue);

            return false;
        },

        /**
         * Retrieve label by value
         *
         * @return {String}
         */
        getLabel: function () {
            var label = '';

            _.each(this.options, function (option) {
                if (this.currentValue == option.value) {
                    label = option.label;
                    return label;
                }
            }, this);

            return label;
        },

        /**
         * Retrieve classes for option
         *
         * @param {Object} option
         * @return {Object}
         */
        getOptionClasses: function (option) {
            var classes = option.additionalClasses;

            if (!_.isObject(classes)) {
                classes = {};
            }
            classes['current'] = this.isActive(option.value);

            return classes;
        },

        /**
         * Check is active value
         *
         * @param {String} value
         * @returns {Object}
         */
        isActive: function (value) {
            return this.currentValue == value;
        },

        /**
         * Retrieve default value
         *
         * @return {String}
         * @private
         */
        getDefaultOption: function () {
            var defaultValue;

            if (_.size(this.options) === 0) {
                return defaultValue;
            }
            _.each(this.options, function (option) {
                if (option.value == this.default) {
                    defaultValue = option.value;
                    return;
                }
            }, this);

            if (_.isEmpty(defaultValue)) {
                return this.options[0].value;
            }

            return defaultValue;
        },

        /**
         * Handler of the applied value changed
         */
        onAppliedChanged: function () {
            this._appliedChanged = true;
            return this;
        },

        /**
         * Handler of the data providers' 'reloaded' event
         */
        onDataReloaded: function () {
            this.updateUrl();
            this._appliedChanged = false;
        },

        /**
         * Update url after reloaded provider data
         */
        updateUrl: function () {
            var filterValue;

            if (this.allowUpdateUrl && this._appliedChanged) {
                filterValue = [{'key': this.filterScope, 'value': this.applied}];
                urlUpdater.updateUrl(url.getSubmitUrl(filterValue));
            }
        }
    });
});
