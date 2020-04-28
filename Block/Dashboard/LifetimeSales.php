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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class LifetimeSales
 * @package Mageplaza\Reports\Block\Dashboard
 */
class LifetimeSales extends AbstractClass
{
    const NAME = 'lifetimeSales';

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Lifetime Sales');
    }

    /**
     * @param bool $includeContainer
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTotal($includeContainer = true)
    {
        $total = $this->_helperData->getLifetimeSales();
        if (isset($total['lifetime'])) {
            return $this->format($total['lifetime'], [], $includeContainer);
        }

        return '';
    }
}
