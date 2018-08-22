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

/**
 * Class LastOrders
 * @package Mageplaza\Reports\Block\Dashboard
 */
class Customers extends AbstractClass
{
    const NAME = 'customers';
    const MAGE_REPORT_CLASS = \Magento\Backend\Block\Dashboard\Tab\Customers\Most::class;

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Customers');
    }

    /**
     * @return string
     */
    public function getDetailUrl()
    {
        if (!$this->_helperData->isProPackage()) {
            return parent::getDetailUrl();
        }
        return $this->getUrl('mpreports/details/salesbycustomer');
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return true;
    }
}