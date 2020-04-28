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
    'Magento_Catalog/js/price-utils',
    'chartBundle'
], function ($, priceUtils) {
    'use strict';
    $.widget('mageplaza.initChart', {
        options: {
            chartData: {
                yUnit: ''
            }
        },
        _create: function () {
            var ctx = $('#' + this.options.chartData.name + '-chart');
            var data = {
                type: 'line',
                data: this.getData(),
                options: this.getOptions()
            };

            new window.Chart(ctx, data);
        },
        getOptions: function () {
            var self = this;

            return {
                fillColor: 'rgba(220,220,220,0.9)',
                legend: {
                    display: true,

                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        fontColor: '#333'
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: true,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var index = tooltipItem.index;

                            return dataset.labels[index] + ': ' +
                                (data.yUnit
                                    ? priceUtils.formatPrice(dataset.data[index], data.yUnit)
                                    : dataset.data[index]);
                        },
                        title: function (tooltipItems, data) {
                            if (data.index === 'repeatCustomerRate') {
                                return tooltipItems[0].xLabel;
                            }
                            return '';
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        display: true,
                        labelString: 'Time',
                        type: 'time',
                        // distribution: 'series',
                        time: {
                            stepSize: this.options.chartData.stepSize,
                            unit: 'day',
                            displayFormats: {
                                day: 'Y-MMM-D'
                            }
                        },
                        ticks: {
                            callback: function (label) {
                                return label;
                            }
                        }
                    }],
                    yAxes: [{
                        scaleStartValue: 0,
                        scaleLabel: {
                            display: true,
                            labelUnit: this.options.chartData.yUnit,
                            labelString: this.options.chartData.yLabel,
                            scaleStepWidth: 1
                        },
                        ticks: {
                            min: 0,
                            // Include a currency sign in the ticks
                            callback: function (value) {
                                if (Math.floor(value) === value) {
                                    return self.options.chartData.yUnit
                                        ? priceUtils.formatPrice(value, self.options.chartData.yUnit)
                                        : value;
                                }
                            }
                        }
                    }]
                }
            };
        },
        getData: function () {
            var data = {
                index: this.options.chartData.name,
                labels: this.options.chartData['labels'],
                yUnit: this.options.chartData['yUnit'],
                datasets: [
                    {
                        labels: this.options.chartData['data']['labels'],
                        label: this.options.chartData['label'][0],
                        data: this.options.chartData['data']['data'],
                        backgroundColor: '#977bca',
                        borderColor: '#6f42c1',
                        borderWidth: 1,
                        lineTension: 0.5, //cong
                        pointBorderWidth: 1,
                        pointBorderColor: 'transparent',
                        fill: this.options.chartData.isFill
                    }
                ]
            };
            var compareDataset = this.getCompareDataSet();

            if (this.options.chartData.isCompare === '1' || this.options.chartData.name === 'repeatCustomerRate') {
                data.datasets.push(compareDataset);
            }

            return data;
        },
        getCompareDataSet: function () {
            return {
                labels: this.options.chartData['compareData']['labels'],
                label: this.options.chartData['label'][1],
                data: this.options.chartData['compareData']['data'],
                backgroundColor: '#59a7b3',
                borderColor: '#17a2b8',
                borderWidth: 1,
                lineTension: 0.5,
                pointBorderColor: 'transparent',
                // pointBackgroundColor: 'transparent',
                // pointHoverBackgroundColor: 'blue',
                fill: this.options.chartData.isFill,
            };
        }
    });

    return $.mageplaza.initChart;
});
