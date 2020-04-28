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

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

/**
 * Class TotalSales
 * @package Mageplaza\Reports\Block\Dashboard
 */
class TotalSales extends AbstractClass
{
    const NAME = 'totalSales';

    /**
     * @var string
     */
    protected $_template = 'dashboard/chart.phtml';

    /**
     * @param $includeContainer
     *
     * @return float|int|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getTotal($includeContainer = true)
    {
        $date   = $this->_helperData->getDateRange();
        $totals = $this->_helperData->getTotalsByDateRange($date[0], $date[1]);

        return $this->getBaseCurrency()->format($totals->getRevenue() ?: 0, [], $includeContainer);
    }

    /**
     * @return float|int
     * @throws LocalizedException
     * @throws Exception
     */
    public function getRate()
    {
        $dates         = $this->_helperData->getDateRange();
        $totals        = $this->_helperData->getTotalsByDateRange($dates[0], $dates[1]);
        $compareTotals = $this->_helperData->getTotalsByDateRange($dates[2], $dates[3]);
        if ((int) $totals->getRevenue() === 0 && (int) $compareTotals->getRevenue() === 0) {
            return 0;
        }
        if ((int) $compareTotals->getRevenue() === 0) {
            return 100;
        }
        if ((int) $totals->getRevenue() === 0) {
            return -100;
        }

        return round((($totals->getRevenue() - $compareTotals->getRevenue()) / $compareTotals->getRevenue()) * 100, 2);
    }

    /**
     * @param $date
     * @param null $endDate
     *
     * @return float|int
     * @throws LocalizedException
     */
    protected function getDataByDate($date, $endDate = null)
    {
        $totals = $this->_helperData->getTotalsByDateRange($date, $endDate);

        return round($totals->getRevenue() ?: 0, 2);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getYUnit()
    {
        return $this->getBasePriceFormat();
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Totals');
    }
}
