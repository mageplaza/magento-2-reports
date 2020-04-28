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

use Magento\Backend\Block\Dashboard\Tab\Products\Viewed;
use Magento\Framework\Phrase;

/**
 * Class MostViewedProducts
 * @package Mageplaza\Reports\Block\Dashboard
 */
class MostViewedProducts extends AbstractClass
{
    const NAME              = 'mostViewedProducts';
    const MAGE_REPORT_CLASS = Viewed::class;

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Most Viewed Products');
    }
}
