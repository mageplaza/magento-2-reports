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
    const NAME              = '';
    const MAGE_REPORT_CLASS = '';

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
     * @var \Magento\Directory\Model\Currency|null
     */
    protected $_currentCurrencyCode = null;

    /**
     * @var
     */
    protected $_currency;

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
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function getContentHtml()
    {
        if (static::MAGE_REPORT_CLASS) {
            return $this->getLayout()->createBlock(static::MAGE_REPORT_CLASS)
                ->toHtml();
        }

        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return false;
    }

    /**
     * Formatting value specific for this store
     *
     * @param $price
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function format($price)
    {
        return $this->getCurrency()->format($price);
    }

    /**
     * Setting currency model
     *
     * @param \Magento\Directory\Model\Currency $currency
     * @return void
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Retrieve currency model if not set then return currency model for current store
     *
     * @return Currency|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrency()
    {
        if ($this->_currentCurrencyCode === null) {
            if ($store = $this->getRequest()->getParam('store')) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore($store)->getBaseCurrency();
            } else if ($website = $this->getRequest()->getParam('website')) {
                $this->_currentCurrencyCode = $this->_storeManager->getWebsite($website)->getBaseCurrency();
            } else if ($group = $this->getRequest()->getParam('group')) {
                $this->_currentCurrencyCode = $this->_storeManager->getGroup($group)->getWebsite()->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getChartData()
    {
        $date = $this->_helperData->getDateRange();
        $days = max($this->_helperData->getDaysByDateRange($date[0], $date[1]), $this->_helperData->getDaysByDateRange($date[2], $date[3]));

        return [
            'label'       => $this->getChartDataLabel(),
            'data'        => $this->getDataByDateRange($date[0], $date[1]),
            'days'        => $days,
            'labels'      => $this->_helperData->getPeriodsDate($date[0], null, $days),
            'stepSize'    => round($days / 6),
            'total'       => $this->getTotal(),
            'rate'        => $this->getRate(),
            'yUnit'       => $this->getYUnit(),
            'yLabel'      => $this->getYLabel(),
            'isFill'      => $this->isFill(),
            'isCompare'   => $this->isCompare(),
            'name'        => $this->getName(),
            'compareData' => $this->isCompare() ? $this->getDataByDateRange($date[2], $date[3]) : []
        ];
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
     * @param $date
     * @param null $endDate
     * @return int
     */
    protected function getDataByDate($date, $endDate = null)
    {
        return rand(1, 10);
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
        return static::NAME;
    }
}