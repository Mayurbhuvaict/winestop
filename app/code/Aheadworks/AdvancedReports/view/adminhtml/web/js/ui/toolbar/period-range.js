define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/lib/view/utils/async',
    'mage/translate',
    'Aheadworks_AdvancedReports/js/url',
    'Aheadworks_AdvancedReports/js/url/updater',
    'uiElement',
    'Magento_Ui/js/modal/alert',
    'uiRegistry',
    'awArepTimeframe'
], function ($, _, utils, async, $t, url, urlUpdater, Element, alert, registry) {
    'use strict';

    /**
     * Removes empty properties from the object
     *
     * @param {Object} data
     * @returns {Object}
     */
    function removeEmpty(data) {
        var result = utils.mapRecursive(data, utils.removeEmptyValues.bind(utils));

        return utils.mapRecursive(result, function (value) {
            return _.isString(value) ? value.trim() : value;
        });
    }

    return Element.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReports/ui/toolbar/period-range',
            compareAvailable: true,
            listens: {
                dateRange: 'onDateRangeValueChanged',
                dateFrom: 'onPeriodValueChanged',
                dateTo: 'onPeriodValueChanged',
                compareEnabled: 'onCompareEnabledValueChanged',
                compareDateRange: 'onCompareDateRangeValueChanged',
                applied: 'onAppliedChanged',
                '${ $.provider }:reloaded': 'onDataReloaded'
            },
            imports: {
                gridParams: '${ $.provider }:params'
            },
            exports: {
                applied: '${ $.provider }:params'
            }
        },
        _switchDateRangeToCustom: true,
        _dateRangeAllowChanged: true,
        _compareDateRangeAllowChanged: true,
        _appliedChanged: false,

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()._bind();

            return this;
        },

        /**
         * Bind
         *
         * @return {PeriodRange} Chainable
         */
        _bind: function () {
            _.bindAll(this, 'initDatePicker', '_periodEndDateChangedByCalendar');
            async.async('div#aw-arep-period-calendars', this.name, this.initDatePicker);

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'dateRange',
                    'dateFrom',
                    'dateTo',
                    'compareEnabled',
                    'compareDateRange',
                    'compareDateFrom',
                    'compareDateTo'
                ]);

            return this;
        },

        /**
         * Sets filters data to the applied state
         *
         * @returns {PeriodRange} Chainable
         */
        apply: function () {
            var params, periodParams;

            if (this.isValid()) {
                periodParams = this._getPeriodParams();

                params = utils.extend({}, this.gridParams, periodParams);
                if (params.period_from !== undefined) {
                    this._setDateNotify(params.period_from);
                }
                this.set('applied', removeEmpty(params));
            } else {
                alert({
                    content: $t('Invalid period values.')
                });
            }

            return this;
        },

        /**
         * Initialize date picker
         *
         * @return {PeriodRange} Chainable
         */
        initDatePicker: function (node) {
            this.datePicker = new Timeframe(node, {
                startField: 'aw-arep-period-date-from',
                endField: 'aw-arep-period-date-to',
                compareStartField: 'aw-arep-compare-date-from',
                compareEndField: 'aw-arep-compare-date-to',
                form: 'aw-arep-period-form',
                earliest: this.earliestDate,
                latest: this.latestDate,
                weekOffset: this.weekOffset
            });

            this.datePicker.parseField('start', true);
            this.datePicker.parseField('end', true);
            this.datePicker.parseField('compareStart', true);
            this.datePicker.parseField('compareEnd', true);
            this.datePicker.selectstart = true;
            this.datePicker.populate().refreshRange();
            $(this.datePicker).on('period-end-date-changed', this._periodEndDateChangedByCalendar);
        },

        /**
         * Retrieve compare period label
         *
         * @return {String}
         */
        getComparePeriodLabel: function () {
            var label = $t('Compared to: {period}');

            return label.replace('{period}', this.compareDateFrom() + ' - ' + this.compareDateTo());
        },

        /**
         * Date range value changed
         */
        onDateRangeValueChanged: function (dateRangeValue) {
            var range;

            if (!this._dateRangeAllowChanged) {
                return ;
            }

            range = _.findWhere(this.dateRangeOptions, {value: dateRangeValue});
            if (_.isObject(range) && !_.isUndefined(range.from) && !_.isUndefined(range.to)) {
                this._switchDateRangeToCustom = false;
                this.dateFrom(range.from);
                this.dateTo(range.to);
                this._switchDateRangeToCustom = true;
            }

            this.datePicker.range.set('start', new Date.parseToObject(this.dateFrom()));
            this.datePicker.range.set('end', new Date.parseToObject(this.dateTo()));
            this.datePicker.parseField('start', false);
            this.datePicker.parseField('end', false);

            if (this.compareAvailable && this.compareEnabled()) {
                this._switchCompareDateRange();
            }
        },

        /**
         * Period changed
         */
        onPeriodValueChanged: function () {
            if (this._switchDateRangeToCustom) {
                this._dateRangeAllowChanged = false;
                this.dateRange('custom');
                this._dateRangeAllowChanged = true;

                if (this.compareAvailable && this.compareEnabled()) {
                    this._switchCompareDateRange();
                }
            }
        },

        /**
         * Compare enabled value changed
         *
         * @param {Boolean} compareEnabledValue
         */
        onCompareEnabledValueChanged: function (compareEnabledValue) {
            if (!this.compareAvailable) {
                return;
            }

            if (compareEnabledValue) {
                this.datePicker.selectstart = true;
                this.datePicker.populate();
                this.datePicker.refreshRange();
                this._switchCompareDateRange();
            } else {
                this._compareDateRangeAllowChanged = false;
                this.compareDateRange(this.defaultCompareType);
                this._compareDateRangeAllowChanged = true;
                this.datePicker.clearCompareRange();
                this._toggleDatePickerCompareState(false, true);
            }
        },

        /**
         * Compare date range value changed
         *
         * @param {String} compareDateRangeValue
         */
        onCompareDateRangeValueChanged: function(compareDateRangeValue) {
            if (!this._compareDateRangeAllowChanged) {
                return;
            }

            if (compareDateRangeValue === 'custom') {
                this._toggleDatePickerCompareState(true, true);
            } else {
                this._switchCompareDateRange();
                this._toggleDatePickerCompareState(false, true);
            }
        },

        /**
         * Date field from click event
         */
        dateFromFieldClick: function () {
            this._toggleDatePickerCompareState(false, true);
        },

        /**
         * Date field to click event
         */
        dateToFieldClick: function () {
            this._toggleDatePickerCompareState(false, false);
        },

        /**
         * Compare date from field click event
         */
        compareDateFromFieldClick: function () {
            this.compareDateRange('custom');
            this._toggleDatePickerCompareState(true, true);
        },

        /**
         * Compare date to field click event
         */
        compareDateToFieldClick: function () {
            this.compareDateRange('custom');
            this._toggleDatePickerCompareState(true, false);
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
            var filterValue = [], periodParams;

            if (this._appliedChanged) {
                periodParams = removeEmpty(this._getPeriodParams());
                _.each(periodParams, function (value, key) {
                    filterValue.push({'key': key, 'value': value});
                });
                urlUpdater.updateUrl(url.getSubmitUrl(filterValue));
            }
        },

        /**
         * Switch compare date range
         */
        _switchCompareDateRange: function() {
            var compareDates = this._resolveCompareDatesByDateRange(),
                validDateFrom = this._getValidDate(compareDates.from),
                validDateTo = this._getValidDate(compareDates.to);

            this.compareDateFrom(validDateFrom.strftime(this.datePicker.format));
            this.compareDateTo(validDateTo.strftime(this.datePicker.format));

            this.datePicker.range.set('compareStart', validDateFrom);
            this.datePicker.range.set('compareEnd', validDateTo);
            this.datePicker.parseField('compareStart', false);
            this.datePicker.parseField('compareEnd', false);

            if (validDateFrom.getTime() != compareDates.from.getTime()
                || validDateTo.getTime() != compareDates.to.getTime()
            ) {
                this.compareDateRange('custom');
            }
        },

        /**
         * Resolve compare dates by date range
         *
         * @return {{from: Date, to: Date}}
         * @private
         */
        _resolveCompareDatesByDateRange: function () {
            var range, compareDateFrom, compareDateTo;

            if (this.dateRange() === 'custom') {
                range = this._getCustomCompareDateRange();
            } else {
                range = _.findWhere(this.dateRangeOptions, {value: this.dateRange()});
                range = this._convertCompareDateRangeToDate(range);
            }

            switch (this.compareDateRange()) {
                case 'previous_period':
                    compareDateFrom = range.cFrom;
                    compareDateTo = range.cTo;
                    break;
                case 'previous_year':
                    compareDateFrom = range.cYearFrom;
                    compareDateTo = range.cYearTo;
                    break;
                default:
                    compareDateFrom = new Date(this.compareDateFrom());
                    compareDateTo = new Date(this.compareDateTo());
            }

            return {from: compareDateFrom, to: compareDateTo};
        },

        /**
         * Convert compare date range from string to dateformat
         *
         * @param {Object} range
         * @return {{cFrom: Date, cTo: Date, cYearFrom: Date, cYearTo: Date}}
         * @private
         */
        _convertCompareDateRangeToDate: function (range) {
            var cFrom = new Date(range.cFrom),
                cTo = new Date(range.cTo),
                cYearFrom = new Date(range.cYearFrom),
                cYearTo = new Date(range.cYearTo);

            return {cFrom: cFrom, cTo: cTo, cYearFrom: cYearFrom, cYearTo: cYearTo};
        },

        /**
         * Retrieve custom compare date range
         *
         * @return {{cFrom: Date, cTo: Date, cYearFrom: Date, cYearTo: Date}}
         * @private
         */
        _getCustomCompareDateRange: function () {
            var oneDay = 86400000,
                cFrom = new Date(this.dateFrom()),
                cTo = new Date(this.dateTo()),
                cYearFrom = new Date(this.dateFrom()),
                cYearTo = new Date(this.dateTo()),
                delta = (cTo.getTime() - cFrom.getTime()) + oneDay;

            cFrom.setTime(cFrom.getTime() - delta);
            cTo.setTime(cTo.getTime() - delta);

            cYearFrom.setFullYear(cYearFrom.getFullYear() - 1);
            cYearTo.setFullYear(cYearTo.getFullYear() - 1);

            return {cFrom: cFrom, cTo: cTo, cYearFrom: cYearFrom, cYearTo: cYearTo};
        },

        /**
         * Check and fix specified date
         *
         * @param {Date} date
         * @return {Date}
         */
        _getValidDate: function(date) {
            var earliestDate = new Date.parseToObject(this.earliestDate),
                latestDate = new Date.parseToObject(this.latestDate),
                validDate = new Date();

            validDate.setTime(date.getTime());

            if (date.getTime() < earliestDate.getTime()) {
                validDate.setTime(earliestDate.getTime());
            }
            if (date.getTime() > latestDate.getTime()) {
                validDate.setTime(latestDate.getTime());
            }

            return validDate;
        },

        /**
         * Toggle Date Picker compare state
         *
         * @param {Boolean} selectCompare
         * @param {Boolean|null} selectStart
         */
        _toggleDatePickerCompareState: function (selectCompare, selectStart) {
            this.datePicker.selectCompare = selectCompare;
            if (selectStart !== null) {
                this.datePicker.selectstart = selectStart;
            }
            this.datePicker.populate();
            this.datePicker.refreshRange();
        },

        /**
         * Retrieve period params
         *
         * @return {Object}
         * @private
         */
        _getPeriodParams: function () {
            var compareParams = {
                    compare_type: 'disabled',
                    compare_from: '',
                    compare_to: ''
                },
                params = {
                    period_type: this.dateRange(),
                    period_from: this._formattedDate(this.dateFrom()),
                    period_to: this._formattedDate(this.dateTo())
                };

            if (this.compareAvailable && this.compareEnabled()) {
                compareParams = {
                    compare_type: this.compareDateRange(),
                    compare_from: this._formattedDate(this.compareDateFrom()),
                    compare_to: this._formattedDate(this.compareDateTo())
                };
            }
            params = utils.extend({}, params, compareParams);

            return params;
        },

        /**
         * Formatted date
         *
         * @param {String} date
         * @returns {string}
         * @private
         */
        _formattedDate: function(date) {
            date = new Date(date);

            return date.getFullYear().toString()
                + '-' + ('0' + (date.getMonth() + 1)).slice(-2)
                + '-' + ('0' + date.getDate()).slice(-2);
        },

        /**
         * Period end date changed by calendar
         */
        _periodEndDateChangedByCalendar: function () {
            if (this.compareAvailable && this.compareEnabled() && this.compareDateRange() == 'custom') {
                this.datePicker.selectCompareStartDate = true;
            }
        },

        /**
         * Check is valid fields
         *
         * @return {boolean}
         */
        isValid: function () {
            var dateFromDate = new Date(this.dateFrom()),
                dateToDate = new Date(this.dateTo()),
                compareFromDate,
                compareToDate;

            if (isNaN(dateFromDate) || isNaN(dateToDate) || dateFromDate > dateToDate) {
                return false;
            }

            if (this.compareAvailable && this.compareEnabled()) {
                compareFromDate = new Date(this.compareDateFrom());
                compareToDate = new Date(this.compareDateTo());

                if (isNaN(compareFromDate) || isNaN(compareToDate) || compareFromDate > compareToDate) {
                    return false;
                }
            }

            return true;
        },

        /**
         * Set date for date-notify message
         *
         * @private
         */
        _setDateNotify: function (date) {
            registry.get('dateNotify', function (dateNotify) {
                dateNotify.setDate(date);
            });
        }
    });
});