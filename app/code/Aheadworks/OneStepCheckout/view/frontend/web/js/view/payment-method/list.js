define(
    [
        'jquery',
        'ko',
        'underscore',
        'Aheadworks_OneStepCheckout/js/view/form/form',
        'uiLayout',
        'mageUtils',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor',
        'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
        'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
        'mage/translate'
    ],
    function (
        $,
        ko,
        _,
        Component,
        layout,
        utils,
        quote,
        methodList,
        rendererList,
        paymentService,
        methodConverter,
        checkoutDataResolver,
        postProcessor,
        paymentMethodsService,
        completenessLogger,
        $t
    ) {
        'use strict';

        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/payment-method/list',
                isPaymentMethodsAvailable: false,
                isLoading: paymentMethodsService.isLoading,
                defaultGroup: {
                    alias: 'payment-methods-items',
                    displayArea: 'payment-methods-items',
                    sortOrder: 100
                },
                invalidBlockSelector: '.aw-onestep-groups_item.payment-methods',
                paymentGroupsList: [],
                modules: {
                    parentComponent: '${ $.parentName }'
                }
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super().initMethodsRenders();

                checkoutDataResolver.resolvePaymentMethod();
                methodList.subscribe(
                    function (changes) {
                        var renders = this._getPaymentRegions(),
                            methods = [],
                            methodsToDelete = [],
                            methodsToAdd = {},
                            sortOrderCounter = 0;

                        checkoutDataResolver.resolvePaymentMethod();

                        _.each(changes, function (change) {
                            var methodCode = change.value.method;

                            if (change.status === 'added') {
                                methodsToAdd[methodCode] = change.value;
                                methods.push(methodCode);
                            }
                        }, this);
                        _.each(changes, function (change) {
                            var methodCode = change.value.method;

                            if (change.status === 'deleted') {
                                methodsToDelete.push(methodCode);
                                if (_.indexOf(methods, methodCode) == -1) {
                                    methods.push(methodCode);
                                }
                            }
                        }, this);

                        _.each(methods, function (methodCode) {
                            var methodRenderer;

                            if (_.indexOf(methodsToDelete, methodCode) != -1
                                && methodsToAdd[methodCode] === undefined
                            ) {
                                this._removeRenderer(methodCode);
                            } else if (methodsToAdd[methodCode] !== undefined
                                && _.indexOf(methodsToDelete, methodCode) == -1
                            ) {
                                this._createRenderer(methodsToAdd[methodCode], sortOrderCounter++);
                            } else {
                                methodRenderer = _.find(renders, function (renderer) {
                                    return renderer.item.method.indexOf(methodCode) === 0;
                                });
                                if (typeof methodRenderer != 'undefined') {
                                    methodRenderer.updateConfig(
                                        methodRenderer.sortOrder,
                                        sortOrderCounter++,
                                        'sortOrder'
                                    );
                                }
                            }
                        }, this);
                    }, this, 'arrayChange');
                quote.paymentMethod.subscribe(function () {
                    this.parentComponent().errorValidationMessage('');
                }, this);
                completenessLogger.bindField('paymentMethod', quote.paymentMethod);

                return this;
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super().
                    observe(['paymentGroupsList']);

                this.isPaymentMethodsAvailable = ko.computed(function () {
                    return methodList().length > 0
                });

                return this;
            },

            /**
             * Create renders for child payment methods
             *
             * @returns {Component}
             */
            initMethodsRenders: function () {
                var sortOrderCounter = 0;

                _.each(methodList(), function (methodData) {
                    this._createRenderer(methodData, sortOrderCounter);
                    sortOrderCounter++;
                }, this);

                return this;
            },

            /**
             * Create payment method renderer
             *
             * @param {Object} methodData
             * @param {number} sortOrder
             */
            _createRenderer: function (methodData, sortOrder) {
                var currentGroup,
                    renderers = this._getRendererForMethod(methodData.method);

                if (_.isArray(renderers)) {
                    _.each(renderers, function (renderer) {
                        currentGroup = renderer.group ? renderer.group : this.defaultGroup;

                        this.collectPaymentGroups(currentGroup);

                        layout([
                            this._createRendererComponent(
                                {
                                    config: renderer.config,
                                    component: renderer.component,
                                    name: renderer.type,
                                    method: methodData.method,
                                    item: methodData,
                                    displayArea: currentGroup.displayArea,
                                    sortOrder: sortOrder
                                }
                            )]
                        );
                    }, this);
                }
            },

            /**
             * Collects unique groups of available payment methods
             *
             * @param {Object} group
             */
            collectPaymentGroups: function (group) {
                var groupsList = this.paymentGroupsList(),
                    isGroupExists = _.some(groupsList, function (existsGroup) {
                        return existsGroup.alias === group.alias;
                    });

                if (!isGroupExists) {
                    groupsList.push(group);
                    groupsList = _.sortBy(groupsList, function (existsGroup) {
                        return existsGroup.sortOrder;
                    });
                    this.paymentGroupsList(groupsList);
                }
            },

            /**
             * Remove payment method renderer
             *
             * @param {string} methodCode
             */
            _removeRenderer: function (methodCode) {
                var items = this._getPaymentRegions();

                _.find(items, function (value) {
                    if (value.item.method.indexOf(methodCode) === 0) {
                        value.disposeSubscriptions();
                        value.destroy();
                    }
                });
            },

            _getPaymentRegions: function () {
                var regions,
                    items = [];

                _.each(this.paymentGroupsList(), function (group) {
                    regions = this.getRegion(group.displayArea)
                    items = _.union(items, regions());
                }, this);

                return items;
            },

            /**
             * Get renderer definition for payment method
             *
             * @param {String} methodCode
             * @returns {Object}
             */
            _getRendererForMethod: function (methodCode) {
                return _.filter(rendererList(), function (renderer) {
                    if (renderer.hasOwnProperty('typeComparatorCallback') &&
                        typeof renderer.typeComparatorCallback == 'function'
                    ) {
                        return renderer.typeComparatorCallback(renderer.type, methodCode);
                    } else {
                        return renderer.type === methodCode;
                    }
                });
            },

            /**
             * Create renderer component definition
             *
             * @param {Object} payment
             * @returns {Object}
             */
            _createRendererComponent: function (payment) {
                var rendererTemplate,
                    rendererComponent,
                    templateData;

                templateData = {
                    parentName: this.name,
                    name: payment.name
                };
                rendererTemplate = {
                    parent: '${ $.$data.parentName }',
                    name: '${ $.$data.name }',
                    displayArea: payment.displayArea,
                    component: payment.component,
                    sortOrder: payment.sortOrder
                };
                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(rendererComponent, {
                    item: payment.item,
                    config: payment.config
                });

                return rendererComponent;
            },

            /**
             * On render list event handler
             */
            onRender: function () {
                postProcessor.initProcessing();
            },

            /**
             * Validate payment method data
             *
             * @returns {boolean}
             */
            validate: function () {
                if (methodList().length > 0 && !quote.paymentMethod()) {
                    this.parentComponent().errorValidationMessage($t('Please specify a payment method.'));
                    this.source.set('params.invalid', true);
                    return false;
                }

                return true;
            }
        });
    }
);
