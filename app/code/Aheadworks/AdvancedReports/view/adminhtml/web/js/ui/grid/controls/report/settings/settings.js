define([
    'underscore',
    'mageUtils',
    'uiCollection'
], function (_, utils, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            applied: {},
            settings: {},
            statefull: {
                applied: true
            },
            listens: {
                applied: 'initSettings',
                elems: 'initAppliedByDefault childrenUpdated'
            },
            exports: {
                applied: '${ $.provider }:params.report_settings'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super().initSettings();

            return this;
        },

        /**
         * Sets settings data to the applied state
         *
         * @returns {Settings} Chainable
         */
        apply: function () {
            if (!this.isValidForm()) {
                this.set('applied', utils.copy(this.prepareSettingsData()));
            }

            return this;
        },

        /**
         * Initialize settings variable to the applied state
         *
         * @returns {Settings} Chainable
         */
        initSettings: function () {
            if (_.isUndefined(this.applied)) {
                this.applied = {};
            }

            this.set('settings', utils.copy(this.applied));
            this.toggleChildrenUseDefault();

            return this;
        },

        /**
         * Called when another element was added to filters collection
         *
         * @returns {Settings} Chainable
         */
        initElement: function (elem) {
            this._super();

            elem.on('isUseDefault', this.onChildrenUseDefault.bind(elem));

            return this;
        },

        /**
         * Initialize applied variable default values (from settings)
         *
         * @returns {Settings} Chainable
         */
        initAppliedByDefault: function () {
            if (!_.keys(this.applied).length && this.initChildCount === this.elems().length) {
                this.set('applied', utils.copy(this.prepareSettingsData()));
            }

            return this;
        },

        /**
         * Validates each element and returns true, if all elements are valid
         *
         * @returns {Boolean}
         */
        isValidForm: function () {
            this.set('params.invalid', false);
            this.trigger('data.validate');

            return this.get('params.invalid');
        },

        /**
         * Prepare data
         *
         * @return {Array}
         */
        prepareSettingsData: function() {
            var data = {};

            data['updated_at_timestamp'] = + new Date;
            _.each(this.elems(), function (elem) {
                if (elem.hasService() && elem.isUseDefault()) {
                    return;
                }

                data[elem.index] = elem.value();
            });

            return data;
        },

        /**
         * Child elements updated in collection
         */
        childrenUpdated: function () {
            if (this.initChildCount === this.elems().length) {
                this.toggleChildrenUseDefault();
            }
        },

        /**
         * Toggle "Use default value" for child elements
         */
        toggleChildrenUseDefault: function() {
            _.each(this.elems(), function (elem) {
                if (!elem.hasService()){
                    return;
                }

                if (!_.isUndefined(this.applied) && !_.isUndefined(this.applied[elem.index])) {
                    elem.isUseDefault(false);
                } else {
                    elem.restoreToDefault();
                    elem.isUseDefault(true);
                }
            }, this);
        },

        /**
         * Is being invoked on children update
         *
         * @param  {Boolean} state
         */
        onChildrenUseDefault: function(state) {
            if (state) {
                this.restoreToDefault();
            }
        }
    });
});
