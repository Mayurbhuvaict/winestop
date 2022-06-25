define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, registry, select) {
    'use strict';

    return select.extend({
        defaults: {
            imports: {
                recordPos: '${ $.parentName }:recordPos'
            },
            listens: {
                recordPos: 'updateIsShowOperatorField'
            },
            modules: {
                conditions: "${ $.ns }.${ $.ns }.listing_conditions",
                record: "${ $.parentName }"
            }
        },

        /**
         * Initialize component
         *
         * @returns {Element}
         */
        initialize: function () {
            this._super()
                .updateDependentElementOptions(true);

            return this;
        },

        /**
         * Initializes observable properties
         *
         * @returns {Attribute} Chainable
         */
        initObservable: function () {
            this._super()
                .observe([
                    'isShowOperatorField'
                ]);
            return this;
        },

        /**
         * Update isShowOperatorField value
         */
        updateIsShowOperatorField: function () {
            this.isShowOperatorField(this.recordPos == 0 ? false : true);
        },

        /**
         * On value change handler
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.updateDependentElementOptions(false);

            return this._super();
        },

        /**
         * Updated options for dependent elements
         *
         * @returns {Attribute} Chainable
         */
        updateDependentElementOptions: function (initialize) {
            registry.get(this.parentName + '.condition', function (component) {
                var attribute = this.getCurrentAttribute();

                component.setOptions(attribute.conditions);
                if (initialize) {
                    if (this.conditions().applied != undefined
                        && this.conditions().applied[this.record().index] != undefined) {
                        component.value(this.conditions().applied[this.record().index].condition);
                    }
                }
            }.bind(this));

            registry.get(this.parentName + '.values.dateValue', function (component) {
                var attribute = this.getCurrentAttribute();

                $('input[name="' + component.inputName + '"]').datepicker('setDate', null);
                component.value('');
                component.error(false);
                component.visible(attribute.type == 'date' ? true : false);
                if (initialize) {
                    if (this.conditions().applied != undefined
                        && this.conditions().applied[this.record().index] != undefined) {
                        component.value(this.conditions().applied[this.record().index].dateValue);
                        $('input[name="' + component.inputName + '"]').datepicker(
                            'setDate', this.conditions().applied[this.record().index].dateValue
                        );
                    }
                }
            }.bind(this));

            registry.get(this.parentName + '.values.inputValue', function (component) {
                var attribute = this.getCurrentAttribute();

                component.value('');
                component.error(false);
                component.visible(attribute.options.length || attribute.type == 'date' ? false : true);
                if (initialize) {
                    if (this.conditions().applied != undefined
                        && this.conditions().applied[this.record().index] != undefined) {
                        component.value(this.conditions().applied[this.record().index].inputValue);
                    }
                }
            }.bind(this));

            registry.get(this.parentName + '.values.selectValue', function (component) {
                var attribute = this.getCurrentAttribute();

                component.value('');
                component.error(false);
                component.setOptions(attribute.options);
                component.visible(attribute.options.length ? true : false);
                if (initialize) {
                    if (this.conditions().applied != undefined
                        && this.conditions().applied[this.record().index] != undefined) {
                        component.value(this.conditions().applied[this.record().index].selectValue);
                    }
                }
            }.bind(this));

            return this;
        },

        /**
         * Retrieve current attribute
         *
         * @returns {Object}
         */
        getCurrentAttribute: function () {
            return _.find(this.options(), function(obj) {
                return obj.value == this.value()
            }, this);
        }
    });
});
