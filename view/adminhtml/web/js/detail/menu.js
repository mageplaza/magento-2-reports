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
    'uiRegistry',
    'daterangepicker'
], function ($, moment, uiRegistry) {
    'use strict';
    var dateRangeEl = $('#daterange');
    var storeSwitcherEl = $('.mp-rp-store-switcher');

    $.widget('mageplaza.menu', {
        _create: function () {
            this.initNowDateRange(moment(this.options.date[0]), moment(this.options.date[1]));
            this.initDateRangeApply();
            this.initStoreSwitcher();
            this.initCustomerGroupSelect();
            this.initPeriodSelect();
            this.initOrderStatusSelect();
            this.initDateUsedSelect();
        },
        initDateRange: function (el, start, end, data) {
            function cb(cbStart, cbEnd) {
                el.find('span').html(cbStart.format('MMM DD, YYYY') + ' - ' + cbEnd.format('MMM DD, YYYY'));
            }

            el.daterangepicker(data, cb);
            cb(start, end);
        },
        initNowDateRange: function (start, end) {
            var dateRangeData = {
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
        initDateRangeApply: function () {
            var self = this;

            dateRangeEl.on('apply.daterangepicker', function (ev, picker) {
                var grid, params;

                self.initNowDateRange(picker.startDate, picker.endDate);
                self.initDateRangeApply();
                grid = uiRegistry.get(self.options.gridName);
                params = grid.get('params');
                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }

                params.mpFilter.startDate = picker.startDate.format('Y-MM-DD');
                params.mpFilter.endDate = picker.endDate.format('Y-MM-DD');
                params.dateRange = [params.mpFilter.startDate, params.mpFilter.endDate, null, null];
                grid.reload();

            });
        },
        initStoreSwitcher: function () {
            var self = this;

            $('[data-role="stores-list"] a').on("click", function (e) {
                var grid = uiRegistry.get(self.options.gridName);
                var params = grid.get('params');
                var data;

                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }
                params.mpFilter.store = $(this).attr('data-value');
                grid.reload();

                data = {
                    store: $(this).attr('data-value')
                };
                $.ajax({
                    method: 'POST',
                    url: self.options.storeUrl,
                    data: data,
                    success: function (res) {
                        storeSwitcherEl.html(res.store);
                        storeSwitcherEl.trigger('contentUpdated');
                        self.initStoreSwitcher();
                    }
                });
                e.stopPropagation();
                e.preventDefault();
            });
        },
        initCustomerGroupSelect: function () {
            var self = this;

            $('.customer-group select').change(function () {
                var grid = uiRegistry.get(self.options.gridName);
                var params = grid.get('params');

                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }
                params.mpFilter['customer_group_id'] = $(this).val();
                grid.reload();
            });
        },
        initPeriodSelect: function () {
            var self = this;

            $('.period select').change(function () {
                var grid = uiRegistry.get(self.options.gridName);
                var params = grid.get('params');

                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }
                params.mpFilter.period = $(this).val();
                grid.reload();
            });
        },
        initDateUsedSelect: function () {
            var self = this;

            $('.date_used select').change(function () {
                var grid = uiRegistry.get(self.options.gridName);
                var params = grid.get('params');

                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }
                params.mpFilter.dateUsed = $(this).val();
                grid.reload();
            });
        },
        initOrderStatusSelect: function () {
            var self = this;
            var cardsTableEl = $('.mp-rp-order-status.admin__action-dropdown-wrap.admin__data-grid-action-columns');

            $('button#mp-rp-order-status').on('click',function () {
                if (cardsTableEl.hasClass('_active')) {
                    cardsTableEl.removeClass('_active');
                } else {
                    cardsTableEl.addClass('_active');
                }
            });
            $('.order_status input').on('click',function () {
                var grid = uiRegistry.get(self.options.gridName);
                var params = grid.get('params');

                if (typeof params.mpFilter === 'undefined') {
                    params.mpFilter = {};
                }
                params.mpFilter.orderStatus = $('#order_status :input').serializeArray();
                grid.reload();
            });
            $('body').on('click',function (e) {
                if (!$(e.target).parents().hasClass('mp-rp-order-status')) {
                    $('.mp-rp-order-status').removeClass('_active');
                }
            });
        }
    });
    return $.mageplaza.menu;
});
