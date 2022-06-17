define([
    'underscore',
    'Aheadworks_AdvancedReports/js/ui/toolbar/bookmarks/dropdown',
    'mage/translate'
], function (_, Dropdown, $t) {
    'use strict';

    return Dropdown.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/toolbar/period-type',
            listens: {
                currentValue: 'onChangePeriod'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super().onChangePeriod();

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .track([
                    'periodLabel',
                    'comparePeriodLabel'
                ]);

            return this;
        },

        /**
         * Retrieve compare period label
         *
         * @return {String}
         */
        getComparePeriodLabel: function () {
            var label = $t('Compared to: {period}');

            return label.replace('{period}', this.comparePeriodLabel);
        },

        /**
         * Handles changes of currentValue variable
         *
         * @return {String}
         */
        onChangePeriod: function () {
            var option = _.findWhere(this.options, {value: this.currentValue});

            this.set('comparePeriodLabel', option.comparePeriod);
            this.set('periodLabel', option.period);
        }
    });
});
