define([
    'ko',
    'underscore',
    'uiCollection',
    'mageUtils',
    'uiLayout'
], function (ko, _, Collection, utils, layout) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/product-attributes/grid/conditions',
            applied: {
                placeholder: true
            },
            recordData: {
                placeholder: true
            },
            columnsCount: 0,
            recordTemplate: 'record',
            templates: {
                record: {
                    parent: '${ $.$data.collection.name }',
                    name: '${ $.$data.index }',
                    dataScope: '${ $.$data.collection.index }.${ $.name }',
                    nodeTemplate: '${ $.parent }.${ $.$data.collection.recordTemplate }'
                }
            },
            statefull: {
                applied: true
            },
            links: {
                recordData: '${ $.provider }:${ $.dataScope }.${ $.index }'
            },
            listens: {
                applied: 'updateAttributes',
                childTemplate: 'columnCounter'
            },
            exports: {
                applied: '${ $.deps }:params.conditions'
            },
        },

        /**
         * Initializes observable properties
         *
         * @returns {Conditions} Chainable
         */
        initObservable: function () {
            this._super()
                .track([
                    'childTemplate'
                ]);
            return this;
        },

        /**
         * Column counter
         */
        columnCounter: function () {
            _.each(this.childTemplate.children, function (cell) {
                this.columnsCount++;
            }, this);
        },

        /**
         * Get number of columns
         *
         * @returns {Number} columns
         */
        getColumnsCount: function () {
            return this.columnsCount;
        },

        /**
         * Sets attributes data to the applied state
         *
         * @returns {Conditions} Chainable
         */
        apply: function() {
            this.validate();

            if (!this.get('params.invalid')) {
                var data = utils.copy(this.recordData);

                this.set('recordData', utils.copy(data));
                this.set('applied', utils.copy(data));
            }

            return this;
        },

        /**
         * Add attribute records and set data to it from applied
         *
         * @returns {Conditions} Chainable
         */
        updateAttributes: function () {
            if (!_.isEqual(this.applied, this.recordData)) {
                var i = 0;

                this.destroyChildren();
                _.each(this.applied, function (record, index) {
                    if (index != 'placeholder') {
                        this.addChild(index, i);
                        i++;
                    }
                }, this);
                this.set('recordData', utils.copy(this.applied));
            }
            return this;
        },

        /**
         * Validates each element and returns true, if all elements are valid
         */
        validate: function () {
            this.set('params.invalid', false);
            this.trigger('data.validate');
        },

        /**
         * Add record
         *
         * @returns {Conditions} Chainable
         */
        addRecord: function() {
            this.addChild(false, false);

            return this;
        },

        /**
         * Add child components
         *
         * @param {Number} index - record(row) index
         *
         * @returns {Conditions} Chainable
         */
        addChild: function (index, recordPos) {
            var template = this.templates.record,
                child;

            index = index || _.isNumber(index) ? index : this.generateUniqueIndex();
            recordPos = recordPos || _.isNumber(recordPos) ? recordPos : this.elems().length;

            _.extend(this.templates.record, {
                recordPos: ko.observable(recordPos)
            });
            child = utils.template(template, {
                collection: this,
                index: index
            });

            layout([child]);

            return this;
        },

        /**
         * Generate unique index for record
         *
         * @returns {String}
         */
        generateUniqueIndex: function () {
            return Math.random().toString(36).substr(2, 9);
        },

        /**
         * Delete record
         *
         * @param {Number} index - row index
         *
         * @returns {Conditions} Chainable
         */
        deleteRecord: function (index) {
            var recordInstance = _.find(this.elems(), function (elem) {
                return elem.index === index;
            });

            this.removeChild(recordInstance);
            recordInstance.destroy();
            this.set('recordData', _.omit(this.recordData, index));
            // Update record position
            this.elems().forEach(function (record, index) {
                record.recordPos(index);
            }, this);

            return this;
        },

        /**
         * Check attribute available
         *
         * @returns boolean
         */
        isAttributesAvailable: function () {
            return Boolean(this.attributesCount);
        }
    });
});
