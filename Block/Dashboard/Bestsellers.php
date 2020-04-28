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

use Magento\Backend\Block\Dashboard\Tab\Products\Ordered;
use Magento\Framework\Phrase;

/**
 * Class Bestsellers
 * @package Mageplaza\Reports\Block\Dashboard
 */
class Bestsellers extends AbstractClass
{
    const NAME              = 'bestsellers';
    const MAGE_REPORT_CLASS = Ordered::class;

    /**
     * @return AbstractClass|void
     */
    protected function _prepareLayout()
    {
        $this->addChild('mp_bestsellers', Ordered::class);
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Bestsellers');
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return true;
    }
}
