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
    'gridstack',
    'gridstackJqueryUi'
], function ($) {
    'use strict';
    $.widget('mageplaza.initGridStack', {
        options: {
            url: ''
        },
        _create: function () {
            var gridStackEl = $('.grid-stack'),
                gridWidget = [];
            var savePositionUrl = this.options.url;

            var options = {
                cellHeight: 20,
                verticalMargin: 10
            };
            gridStackEl.gridstack(options);
            var grid = gridStackEl.data('gridstack');
            //save card position on change
            gridStackEl.on('change', function (event, items) {
                var data = {};
                for (var item of items) {
                    data[item.id] = {
                        'x': item.x,
                        'y': item.y,
                        'width': item.width,
                        'height': item.height
                    }
                }

                saveCardPosition(data);
            });

            $('.grid-stack-item').draggable({cancel: ".not-draggable"});

            var cardsTableEl = $('.mp-ar-card.admin__action-dropdown-wrap.admin__data-grid-action-columns');
            $('button#mp-ar-card').click(function () {
                if (cardsTableEl.hasClass('_active')) {
                    cardsTableEl.removeClass('_active');
                } else {
                    cardsTableEl.addClass('_active');
                }
            });
            $('body').click(function (e) {
                if (!$(e.target).parents().hasClass('mp-ar-card')) {
                    cardsTableEl.removeClass('_active');
                }
            });

            $('.admin__action-dropdown-menu-content .admin__control-checkbox').each(function () {
                $(this).change(function () {
                    var cartId = $(this).attr('cart-id'),
                        cardEl = $('#' + cartId);
                    if (this.checked) {
                        if (cardEl.length) {
                            cardEl.removeClass('hide');
                            grid.addWidget(cardEl);
                        } else if (gridWidget[cartId]) {
                            cardEl = gridWidget[cartId];
                            grid.addWidget(cardEl);
                        }
                        cardEl.draggable({cancel: ".not-draggable"});
                    } else {
                        gridWidget[cartId] = cardEl.attr('data-gs-y', 100).attr('data-gs-x', 100);
                        grid.removeWidget(cardEl);
                    }
                    var data = {};
                    data[cartId] = {
                        'visible': this.checked ? 1 : 0,
                        'x': cardEl.attr('data-gs-x'),
                        'y': cardEl.attr('data-gs-y'),
                        'width': cardEl.attr('data-gs-width'),
                        'height': cardEl.attr('data-gs-height')
                    };
                    // save card position when show/hide card
                    saveCardPosition(data);
                });
            });

            var saveCardPosition = function (data) {
                $.ajax({
                    url: savePositionUrl,
                    data: {items: data},
                    type: 'POST',
                    success: function (res) {
                    },
                    error: function (res) {
                    }
                });
            }
        }
    });

    return $.mageplaza.initGridStack;
});
