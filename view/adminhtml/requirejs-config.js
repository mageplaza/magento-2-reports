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

var config = {
    paths: {
        gridstack: 'Mageplaza_Reports/js/lib/gridstack',
        'gridstack.jqueryui': 'Mageplaza_Reports/js/lib/gridstack.jQueryUI',
        lodash: 'Mageplaza_Reports/js/lib/lodash.min',
        daterangepicker: 'Mageplaza_Reports/js/lib/daterangepicker',
        ar_moment: 'Mageplaza_Reports/js/lib/moment.min',
        ar_momentTimezone: 'Mageplaza_Reports/js/lib/moment-timezone-with-data',
        chart: 'Mageplaza_Reports/js/lib/Chart.bundle',
        mpdaterangepicker: 'Mageplaza_Reports/js/dashboard/mpdaterangepicker',
        initGridStack: 'Mageplaza_Reports/js/dashboard/initGridStack',
        initChart: 'Mageplaza_Reports/js/dashboard/initChart',

    },
    map: {
        '*': {
            'jquery-ui/data': 'jquery/ui',
            'jquery-ui/disable-selection': 'jquery/ui',
            'jquery-ui/focusable': 'jquery/ui',
            'jquery-ui/form': 'jquery/ui',
            'jquery-ui/ie': 'jquery/ui',
            'jquery-ui/keycode': 'jquery/ui',
            'jquery-ui/labels': 'jquery/ui',
            'jquery-ui/jquery-1-7': 'jquery/ui',
            'jquery-ui/plugin': 'jquery/ui',
            'jquery-ui/safe-active-element': 'jquery/ui',
            'jquery-ui/safe-blur': 'jquery/ui',
            'jquery-ui/scroll-parent': 'jquery/ui',
            'jquery-ui/tabbable': 'jquery/ui',
            'jquery-ui/unique-id': 'jquery/ui',
            'jquery-ui/version': 'jquery/ui',
            'jquery-ui/widget': 'jquery/ui',
            'jquery-ui/widgets/mouse': 'jquery/ui',
            'jquery-ui/widgets/draggable': 'jquery/ui',
            'jquery-ui/widgets/droppable': 'jquery/ui',
            'jquery-ui/widgets/resizable': 'jquery/ui',
        }
    },
    shim: {
        gridstack: ['jquery', 'jquery/ui'],
        'gridstack.jqueryui': ['jquery', 'jquery/ui'],
        daterangepicker: ['jquery', 'jquery/ui', 'ar_moment'],
        mpdaterangepicker: ['jquery', 'jquery/ui', 'ar_moment'],
        chart: ['jquery', 'jquery/ui', 'ar_moment'],
        initGridStack: ['jquery', 'gridstack'],
        ar_momentTimezone: ['ar_moment']
    }
};
