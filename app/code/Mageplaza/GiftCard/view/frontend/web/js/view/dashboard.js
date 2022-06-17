/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Mageplaza_GiftCard/js/model/dashboard',
        'Mageplaza_GiftCard/js/action/check-code-availability',
        'Mageplaza_GiftCard/js/action/redeem',
        'Mageplaza_GiftCard/js/action/add-list',
        'Mageplaza_GiftCard/js/action/save-settings',
        'mage/template',
        'text!Mageplaza_GiftCard/template/list/view.html',
        'Magento_Ui/js/modal/confirm',
        'rjsResolver',
        'Magento_Ui/js/modal/modal',
        'mage/validation',
        'mage/translate'
    ],
    function (
        $,
        ko,
        Component,
        giftCard,
        checkCodeAvailability,
        redeemAction,
        addListAction,
        saveSettingsAction,
        mageTemplate,
        savedListViewTemplate,
        confirm,
        resolver
    ) {
        'use strict';

        return Component.extend({
            isLoading: ko.observable(false),
            isTransactionLoading: ko.observable(false),
            isListLoading: ko.observable(false),
            isSettingLoading: ko.observable(false),
            giftcode: ko.observable(null),
            creditNotificationEnable: giftCard.notification.creditEnable,
            creditNotification: ko.observable(giftCard.notification.creditNotification),
            giftcardNotification: ko.observable(giftCard.notification.giftcardNotification),
            isCodeChecked: ko.observable(false),
            canRedeem: ko.observableArray(false),
            balance: giftCard.balance,
            transactions: giftCard.transactions,
            giftCardList: giftCard.giftCardLists,
            defaults: {
                template: 'Mageplaza_GiftCard/dashboard'
            },

            /**
             * Init
             */
            initialize: function () {
                var self = this;

                this._super();

                this.giftcode.subscribe(function () {
                    self.isCodeChecked(false);
                });

                this.creditNotification.subscribe(function (value) {
                    saveSettingsAction(self.isSettingLoading, {credit_notification: value});
                });

                this.giftcardNotification.subscribe(function (value) {
                    saveSettingsAction(self.isSettingLoading, {giftcard_notification: value});
                });

                resolver(function () {
                    if (giftCard.code) {
                        self.giftcode(giftCard.code);
                        self.checkCode();
                    }
                });
            },

            /**
             * Is enable Credit balance
             *
             * @returns {*}
             */
            isEnableCredit: function () {
                return giftCard.isEnableCredit();
            },

            /**
             * Can show redeem button
             *
             * @returns {*}
             */
            isEnableRedeemButton: function () {
                return this.isCodeChecked() && this.canRedeem();
            },

            /**
             * Is enable setting fieldset
             *
             * @returns {*}
             */
            isEnableSetting: function () {
                return giftCard.isEnableSetting();
            },

            /**
             * Ajax check code before redeem/addList
             */
            checkCode: function () {
                var self = this;

                if (typeof this.checkForm === 'undefined') {
                    this.checkForm = $('#check-code-form');
                    this.checkForm.validation();
                }

                if (this.checkForm.valid()) {
                    this.giftcode(this.giftcode().toUpperCase());
                    this.isRequestComplete = $.Deferred();
                    this.isLoading(true);

                    checkCodeAvailability(this.isRequestComplete, this.giftcode(), this.canRedeem);

                    $.when(this.isRequestComplete).done(function () {
                        self.isCodeChecked(true);
                    }).always(function () {
                        self.isLoading(false);
                    });
                }
            },

            /**
             * Redeem Gift Card Code
             */
            redeem: function () {
                var self = this;

                if (this.checkForm.valid()) {
                    this.giftcode(this.giftcode().toUpperCase());
                    this.isRequestComplete = $.Deferred();
                    this.isLoading(true);
                    this.isTransactionLoading(true);

                    redeemAction(this.isRequestComplete, this.giftcode());

                    $.when(this.isRequestComplete).done(function () {
                        self.isCodeChecked(false);
                        self.giftcode('');
                    }).always(function () {
                        self.isLoading(false);
                        self.isTransactionLoading(false);
                    });
                }
            },

            /**
             * Add Gift Code to list
             */
            addList: function () {
                var self = this;

                if (this.checkForm.valid()) {
                    this.giftcode(this.giftcode().toUpperCase());
                    this.isRequestComplete = $.Deferred();
                    this.isLoading(true);
                    this.isListLoading(true);

                    addListAction(this.isRequestComplete, this.giftcode());

                    $.when(this.isRequestComplete).done(function () {
                        self.isCodeChecked(false);
                        self.giftcode('');
                    }).always(function () {
                        self.isLoading(false);
                        self.isListLoading(false);
                    });
                }
            },

            /**
             * View gift card on saved list
             *
             * @param data
             */
            viewGiftCard: function (data) {
                var modalHtml, previewPopup;

                this.initGiftCardViewLabel(data);

                modalHtml    = mageTemplate(savedListViewTemplate, data);
                previewPopup = $('<div></div>').html(modalHtml);

                previewPopup.modal({
                    title: data.code,
                    innerScroll: true,
                    modalClass: '_image-box',
                    buttons: [],
                    clickableOverlay: true
                }).trigger('openModal');
            },

            /**
             * Redeem gift card from saved list
             *
             * @param code
             */
            redeemGiftCard: function (code) {
                var self = this;

                confirm({
                    content: $.mage.__('Are you sure?'),
                    actions: {
                        /**
                         * 'Confirm' action handler.
                         */
                        confirm: function () {
                            self.isRequestComplete = $.Deferred();
                            self.isLoading(true);
                            self.isTransactionLoading(true);
                            self.isListLoading(true);

                            redeemAction(self.isRequestComplete, code);

                            $.when(self.isRequestComplete).always(function () {
                                self.isLoading(false);
                                self.isTransactionLoading(false);
                                self.isListLoading(false);
                            });
                        }
                    }
                });
            },

            /**
             * Remove gift card from saved list
             *
             * @param code
             */
            removeGiftCard: function (code) {
                var self = this;

                confirm({
                    content: $.mage.__('Are you sure?'),
                    actions: {
                        /**
                         * 'Confirm' action handler.
                         */
                        confirm: function () {
                            self.isRequestComplete = $.Deferred();
                            self.isLoading(true);
                            self.isListLoading(true);

                            addListAction(self.isRequestComplete, code, true);

                            $.when(self.isRequestComplete).always(function () {
                                self.isLoading(false);
                                self.isListLoading(false);
                            });
                        }
                    }
                });
            },

            /**
             * Init Label for view popup
             * @param data
             */
            initGiftCardViewLabel: function (data) {
                data.balance_title    = $.mage.__('Balance');
                data.status_title     = $.mage.__('Status');
                data.expired_at_title = $.mage.__('Expired Date');
                data.history_title    = $.mage.__('History');

                data.column_date   = $.mage.__('Date');
                data.column_amount = $.mage.__('Amount');
                data.column_status = $.mage.__('Status');
                data.column_action = $.mage.__('Action');
                data.column_detail = $.mage.__('Detail');
            },

            /**
             * Gift code can print
             *
             * @param data
             */
            canPrint: function (data) {
                if (Number(data.status) !== 1) {
                    return false;
                }

                if (Number(data.delivery_method) !== 3) {
                    return false;
                }

                if (data.delivery_address !== giftCard.email) {
                    return false;
                }

                return !!data.template_id;
            },

            /**
             * show/hide code
             * @param data
             * @param event
             */
            showHideCode: function (data, event) {
                var el = $(event.target);

                if (el.data('display') === 'hidden') {
                    el.html(data.code);
                    el.data('display', 'show');
                } else {
                    el.html(data.hidden_code);
                    el.data('display', 'hidden');
                }
            },

            /**
             * Print gift card
             *
             * @param data
             */
            printGiftCard: function (data) {
                var url = giftCard.baseUrl + 'mpgiftcard/index/printPDF/?id=' + data.giftcard_id;

                window.location.assign(url, '_blank');
            }
        });
    }
);

