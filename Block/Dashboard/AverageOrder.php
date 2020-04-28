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
 * Class AverageOrder
 * @package Mageplaza\Reports\Block\Dashboard
 */
class AverageOrder extends AbstractClass
{
    const NAME = 'averageOrder';

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Average Order');
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
        if (isset($total['average'])) {
            return $this->format($total['average'], [], $includeContainer);
        }

        return '';
    }
}
