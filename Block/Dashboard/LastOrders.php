<?php
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

namespace Mageplaza\Reports\Block\Dashboard;

use Magento\Backend\Block\Dashboard\Orders\Grid;
use Magento\Framework\Phrase;

/**
 * Class LastOrders
 * @package Mageplaza\Reports\Block\Dashboard
 */
class LastOrders extends AbstractClass
{
    const NAME              = 'lastOrders';
    const MAGE_REPORT_CLASS = Grid::class;

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Last Order');
    }
}
