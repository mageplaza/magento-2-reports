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
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'moment',
    'daterangepicker'
], function ($, moment) {
    'use strict';

    var dateRangeEl = $('#daterange'),
        compareDateRangeEl = $('#compare-daterange'),
        dashboardContainer = $('.dashboard-container');

    $.widget('mageplaza.initDateRange', {
        options: {
            url: '',
            isCompare: ''
        },

        _create: function () {
            this.initNowDateRange();
            if (this.options.isCompare) {
                this.initCompareDateRange();
                this.initNowDateRangeHideObserver();
            }
            this.initNowDateRangeObserver();
        },

        initDateRange: function (el, start, end, data) {
            function cb(cbStart, cbEnd) {
                el.find('span').html(cbStart.format('MMM DD, YYYY') + ' - ' + cbEnd.format('MMM DD, YYYY'));
            }

            el.daterangepicker(data, cb);
            cb(start, end);
        },

        initNowDateRange: function () {
            var start = moment(this.options.date[0]),
                end = moment(this.options.date[1]),
                dateRangeData = {
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [
                            moment().subtract(1, 'month').startOf('month'),
                            moment().subtract(1, 'month').endOf('month')
                        ],
                        'YTD': [moment().subtract(1, 'year'), moment()],
                        '2YTD': [moment().subtract(2, 'year'), moment()]
                    }
                };

            this.initDateRange(dateRangeEl, start, end, dateRangeData);
        },

        initCompareDateRange: function (initStartDate, initEndDate) {
            var self = this,
                startDate = initStartDate || dateRangeEl.data().startDate,
                endDate = initEndDate || dateRangeEl.data().endDate,
                days = endDate.diff(startDate, 'days'),
                compareEndDate = moment(startDate.format('Y-MM-DD')).subtract(1, 'days'),
                compareStartDate = moment(compareEndDate.format('Y-MM-DD')).subtract(days, 'days').endOf('days'),
                compareDateRangeData = {
                    startDate: compareStartDate,
                    endDate: compareEndDate,
                    ranges: {
                        'Previous period': [compareStartDate, compareEndDate],
                        'Previous year': [
                            moment(startDate.format('Y-MM-DD')).subtract(1, 'year'),
                            moment(endDate.format('Y-MM-DD')).subtract(1, 'year')
                        ]
                    }
                };

            this.initDateRange(compareDateRangeEl, compareStartDate, compareEndDate, compareDateRangeData);

            compareDateRangeEl.on('apply.daterangepicker', function (ev, picker) {
                compareDateRangeEl.data().startDate = picker.startDate;
                compareDateRangeEl.data().endDate = picker.endDate;
                var data = {
                    dateRange: {
                        0: dateRangeEl.data().startDate.format('Y-MM-DD H:m:s'),
                        1: dateRangeEl.data().endDate.format('Y-MM-DD H:m:s'),
                        2: picker.startDate.format('Y-MM-DD H:m:s'),
                        3: picker.endDate.format('Y-MM-DD H:m:s')
                    }
                };

                self.ajaxSubmit(data);
            });
        },

        ajaxSubmit: function (data) {
            $.ajax({
                url: this.options.url,
                data: data,
                type: 'POST',
                showLoader: true,
                success: function (res) {
                    var dashboard = $('<div>' + res.dashboard + '</div>').find('.dashboard-container');

                    dashboardContainer.html(dashboard.html());
                    dashboardContainer.trigger('contentUpdated');
                },
            });
        },

        initNowDateRangeObserver: function () {
            var self = this;

            dateRangeEl.on('apply.daterangepicker', function (ev, picker) {
                var data;

                dateRangeEl.data().startDate = picker.startDate;
                dateRangeEl.data().endDate = picker.endDate;
                data = {
                    dateRange: {
                        0: picker.startDate.format('Y-MM-DD H:m:s'),
                        1: picker.endDate.format('Y-MM-DD H:m:s'),
                        2: self.options.isCompare ? compareDateRangeEl.data().startDate.format('Y-MM-DD H:m:s') : null,
                        3: self.options.isCompare ? compareDateRangeEl.data().endDate.format('Y-MM-DD H:m:s') : null
                    }
                };

                self.ajaxSubmit(data);
            });
        },

        initNowDateRangeHideObserver: function () {
            var self = this;

            dateRangeEl.on('hide.daterangepicker', function (ev, picker) {
                self.initCompareDateRange(picker.startDate, picker.endDate);
            });
        }
    });

    return $.mageplaza.initDateRange;
});
