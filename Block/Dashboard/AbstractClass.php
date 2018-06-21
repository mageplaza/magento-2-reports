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

use Magento\Backend\Block\Template;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ObjectManager;
use Mageplaza\Reports\Helper\Data;

/**
 * Class AbstractClass
 * @package Mageplaza\Reports\Block\Dashboard
 */
abstract class AbstractClass extends Template
{
    const NAME = '';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var Currency
     */
    protected $baseCurrency;

    /**
     * @var string Price Format
     */
    protected $basePriceFormat;

    /**
     * @var string
     */
    protected $_template = 'dashboard/default.phtml';

    /**
     * AbstractClass constructor.
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_helperData = $helperData;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getChartData()
    {
        $data          = [];
        $date          = $this->_helperData->getDateRange();
        $data['label'] = $this->getChartDataLabel();
        $data['data']  = $this->getDataByDateRange($date[0], $date[1]);
        if ($this->isCompare()) {
            $data['compareData'] = $this->getDataByDateRange($date[2], $date[3]);
        } else {
            $data['compareData'] = [];
        }
        $data['days']      = $days = max($this->_helperData->getDaysByDateRange($date[0], $date[1]),
            $this->_helperData->getDaysByDateRange($date[2], $date[3]));
        $data['labels']    = $this->_helperData->getPeriodsDate($date[0], null, $days);
        $data['stepSize']  = round($days / 6);
        $data['total']     = $this->getTotal();
        $data['rate']      = $this->getRate();
        $data['yUnit']     = $this->getYUnit();
        $data['yLabel']    = $this->getYLabel();
        $data['isFill']    = $this->isFill();
        $data['isCompare'] = $this->isCompare();
        $data['name']      = $this->getName();

        return $data;
    }

    /**
     * @return mixed
     */
    protected function getBaseCurrency()
    {
        if (!$this->baseCurrency) {
            $code = $this->_storeManager->getStore()->getBaseCurrencyCode();

            $this->baseCurrency = ObjectManager::getInstance()->get(Currency::class)->load($code);
        }

        return $this->baseCurrency;
    }

    /**
     * @return mixed
     */
    protected function getBasePriceFormat()
    {
        if (!$this->basePriceFormat) {
            $code = $this->getBaseCurrency()->getCode();

            $this->basePriceFormat = ObjectManager::getInstance()->get(\Magento\Framework\Locale\FormatInterface::class)
                ->getPriceFormat(null, $code);
        }

        return $this->basePriceFormat;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    protected function getDataByDateRange($startDate, $endDate)
    {
        $data = [];
        while (strtotime($endDate) >= strtotime($startDate)) {
            $data['labels'][] = date('Y-m-d', strtotime($startDate));
            $data['data'][]   = $this->getDataByDate($startDate);
            $startDate        = strtotime('+1 day', strtotime($startDate));
            $startDate        = date('Y-m-d H:i:s', $startDate);
        }

        return $data;
    }

    /**
     * @return int
     */
    protected function getTotal()
    {
        return rand(1, 1000);
    }

    /**
     * @param $date
     * @param null $endDate
     * @return int
     */
    protected function getDataByDate($date, $endDate = null)
    {
        return rand(1, 10);
    }

    /**
     * @return int
     */
    protected function getRate()
    {
        return rand(-100, 100);
    }

    /**
     * @return array
     */
    protected function getDateRange()
    {
        return $this->_helperData->getDateRange();
    }

    /**
     * @return string
     */
    protected function getYLabel()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getYUnit()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function isEnabledChart()
    {
        return $this->_helperData->isEnabledChart();
    }

    /**
     * @return bool
     */
    public function isCompare()
    {
        return $this->_helperData->isCompare();
    }

    /**
     * @return bool
     */
    protected function isFill()
    {
        return false;
    }

    /**
     * @return array
     */
    protected function getChartDataLabel()
    {
        $date = $this->_helperData->getDateRange();

        return [date('Y-m-d', strtotime($date[0])) . ' to ' . date('Y-m-d', strtotime($date[1])),
                date('Y-m-d', strtotime($date[2])) . ' to ' . date('Y-m-d', strtotime($date[3]))];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this::NAME;
    }

    /**
     * @return bool
     */
    public function canShowTitle()
    {
        return true;
    }

    public function canShowDetail(){
        return false;
    }
}