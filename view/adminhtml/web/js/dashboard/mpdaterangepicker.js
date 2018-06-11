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
    'ar_momentTimezone',
    'daterangepicker',
], function ($, moment) {
    'use strict';

    var overlayEl = $('.ar_overlay'),
        dateRangeEl = $('#daterange'),
        compareDateRangeEl = $('#compare-daterange'),
        dashboardContainer = $('.dashboard-container');
    $.widget('mageplaza.mpdaterangepicker', {
        options: {
            url: '',
            isCompare: '',
        },

        _create: function () {
            this.initNowDateRange();
            if (this.options.isCompare) {
                this.initCompareDateRange();
                this.initNowDateRangeHideObserver();
            }
            this.initApplyDateRangeObserver();
        },

        initDateRange: function (el, start, end, data) {
            function cb(start, end) {
                el.find('span').html(start.format('MMM DD, YYYY') + ' - ' + end.format('MMM DD, YYYY'));
            }

            el.daterangepicker(data, cb);
            cb(start, end);
        },

        initNowDateRange: function () {
            var start = moment(moment().tz(this.options.timezone).subtract(31, 'days').format('Y-MM-DD')),
                end = moment(moment().tz(this.options.timezone).format('Y-MM-DD')),
                dateRangeData = {
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                };
            this.initDateRange(dateRangeEl, start, end, dateRangeData);
        },

        initCompareDateRange: function () {
            var startDate = dateRangeEl.data().startDate,
                endDate = dateRangeEl.data().endDate,
                days = endDate.diff(startDate, 'days'),
                compareEndDate = moment(startDate.format('Y-MM-DD')).subtract(1, 'days'),
                compareStartDate = moment(compareEndDate.format('Y-MM-DD')).subtract(days, 'days').endOf('days'),
                compareDateRangeData = {
                    startDate: compareStartDate,
                    endDate: compareEndDate,
                    ranges: {
                        'Previous period': [compareStartDate, compareEndDate],
                        // 'Previous week': [moment(startDate.format('Y-MM-DD')).subtract(35, 'days'), moment(startDate.format('Y-MM-DD')).subtract(28, 'days')],
                        // 'Previous 4 week': [moment().subtract(35, 'days'), moment().subtract(28, 'days')],
                        // 'Previous month': [moment().startOf('month').subtract(1, 'months'), moment().endOf('month').subtract(1, 'months')],
                        'Previous year': [moment(startDate.format('Y-MM-DD')).subtract(1, 'year'), moment(endDate.format('Y-MM-DD')).subtract(1, 'year')],
                    }
                };
            this.initDateRange(compareDateRangeEl, compareStartDate, compareEndDate, compareDateRangeData);
        },
        ajaxSubmit: function (data) {
            overlayEl.show();
            $.ajax({
                url: this.options.url,
                data: data,
                type: 'POST',
                success: function (res) {
                    var dashboard = $('<div>' + res.dashboard + '</div>').find('.dashboard-container');
                    dashboardContainer.html(dashboard.html());
                    dashboardContainer.trigger('contentUpdated');
                },
                complete: function () {
                    overlayEl.hide();
                }
            })
        },

        initNowDateRangeHideObserver: function () {
            var self = this;
            dateRangeEl.on('hide.daterangepicker', function (ev, picker) {
                var startDate = picker.startDate,
                    endDate = picker.endDate,
                    days = endDate.diff(startDate, 'days'),
                    compareEndDate = moment(picker.startDate.format('Y-MM-DD')).subtract(1, 'days'),
                    compareStartDate = moment(compareEndDate.format('Y-MM-DD')).subtract(days, 'days'),
                    compareDateRangeData = {
                        startDate: compareStartDate,
                        endDate: compareEndDate,
                        ranges: {
                            'Previous period': [compareStartDate, compareEndDate],
                            // 'Previous week': [moment(dateRangeEl.data().startDate.format('Y-MM-DD')).subtract(35, 'days'), moment(dateRangeEl.data().startDate.format('Y-MM-DD')).subtract(28, 'days')],
                            // 'Previous 4 week': [moment().subtract(35, 'days'), moment().subtract(28, 'days')],
                            // 'Previous month': [moment().startOf('month').subtract(1, 'months'), moment().endOf('month').subtract(1, 'months')],
                            'Previous year': [moment(startDate.format('Y-MM-DD')).subtract(1, 'year'), moment(endDate.format('Y-MM-DD')).subtract(1, 'year')],
                        }
                    };

                dateRangeEl.data().startDate = picker.startDate;
                dateRangeEl.data().endDate = picker.endDate;
                self.initDateRange(compareDateRangeEl, compareStartDate, compareEndDate, compareDateRangeData);
                self.initApplyDateRangeObserver();
            });
        },

        initApplyDateRangeObserver: function () {
            var self = this;
            if (!this.options.isCompare) {
                dateRangeEl.on('apply.daterangepicker', function (ev, picker, moment) {
                    var data = {
                        dateRange: {
                            0: picker.startDate.format('Y-MM-DD H:m:s'),
                            1: picker.endDate.format('Y-MM-DD H:m:s'),
                            2: null,
                            3: null,
                        }
                    };
                    self.ajaxSubmit(data);
                });
            } else {
                compareDateRangeEl.on('apply.daterangepicker', function (ev, picker) {
                    var data = {
                        dateRange: {
                            0: dateRangeEl.data().startDate.format('Y-MM-DD H:m:s'),
                            1: dateRangeEl.data().endDate.format('Y-MM-DD H:m:s'),
                            2: picker.startDate.format('Y-MM-DD H:m:s'),
                            3: picker.endDate.format('Y-MM-DD H:m:s'),
                        }
                    };
                    self.ajaxSubmit(data);
                });
            }
        }
    });

    return $.mageplaza.mpdaterangepicker;
});
