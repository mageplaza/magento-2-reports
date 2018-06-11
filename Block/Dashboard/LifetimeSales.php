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
 * Class LifetimeSales
 * @package Mageplaza\Reports\Block\Dashboard
 */
class LifetimeSales extends AbstractClass
{
    const NAME = 'lifetimeSales';

    /**
     * @return AbstractClass|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $block = $this->getLayout()->createBlock('Magento\Backend\Block\Dashboard\Sales')
            ->setTemplate('Mageplaza_Reports::dashboard/bar.phtml');

        $this->setChild('mp_lifetimeSales', $block);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Lifetime Sales');
    }

    /**
     * @return bool
     */
    public function canShowTitle()
    {
        return false;
    }
}