define(
    [
        'ko',
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/totals',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Aheadworks_OneStepCheckout/js/model/gift-message-service'
    ],
    function (ko, _, Component, totals, getSectionsDetailsAction, giftMessageService) {
        'use strict';

        /**
         * Initial sorted items
         */
        var initialItems = (totals.getItems())();

        /**
         * Sort cart items according to initial positions
         *
         * @param {Array} items
         */
        function sortItems(items) {
            var sortedItems = [],
                diff = [];

            _.each(items, function (item) {
                var founded = _.find(initialItems, function (initItem) {
                    return item.item_id == initItem.item_id;
                });

                if (founded === undefined) {
                    diff.push(item);
                }
            });
            _.each(initialItems, function (initialItem) {
                var candidate = _.find(items, function (newItem) {
                    return initialItem.item_id == newItem.item_id;
                });

                if (candidate !== undefined) {
                    sortedItems.push(candidate);
                } else if (diff.length > 0) {
                    candidate = diff.pop();
                    sortedItems.push(candidate);
                }
            });
            if (diff.length > 0) {
                _.each(diff, function (item) {
                    sortedItems.push(item);
                });
            }
            initialItems = sortedItems;

            return sortedItems;
        }

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/cart-items',
                items: sortItems(initialItems),
                itemsQty: parseFloat(totals.totals().items_qty),
                isExpanded: window.checkoutConfig.isMiniCartExpanded
            },
            maxCartItemsToDisplay: window.checkoutConfig.maxCartItemsToDisplay,
            cartUrl: window.checkoutConfig.cartUrl,

            /**
             * Returns count of cart line items
             *
             * @returns {Number}
             */
            getCartLineItemsCount: function () {
                return parseInt(totals.getItems()().length, 10);
            },

            /**
             * Returns cart items qty
             *
             * @returns {Number}
             */
            getItemsQty: function () {
                return parseFloat(this.totals['items_qty']);
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                var self = this;

                this._super();
                totals.getItems().subscribe(function (newItems) {
                    self.setItems(sortItems(newItems));
                });
                totals.totals.subscribe(function (newTotals) {
                    self.itemsQty(parseFloat(newTotals.items_qty));
                });
                giftMessageService.isLoading(true);
                getSectionsDetailsAction(['giftMessage']).always(function () {
                    giftMessageService.isLoading(false);
                });
            },

            /**
             * Set items to observable field
             *
             * @param {Object} items
             */
            setItems: function (items) {
                if (items && items.length > 0) {
                    items = items.slice(parseInt(-this.maxCartItemsToDisplay, 10));
                }
                this.items(items);
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();
                this.observe(['items','itemsQty']);

                return this;
            }
        });
    }
);
