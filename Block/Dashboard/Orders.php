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
use Magento\Sales\Model\OrderFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Transactions
 * @package Mageplaza\Reports\Block\Dashboard
 */
class Orders extends AbstractClass
{
    const NAME = 'orders';

    /**
     * @var string
     */
    protected $_template = 'dashboard/chart.phtml';

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * Transactions constructor.
     * @param Template\Context $context
     * @param OrderFactory $orderFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        OrderFactory $orderFactory,
        Data $helperData,
        array $data = [])
    {
        parent::__construct($context, $helperData, $data);

        $this->_orderFactory = $orderFactory;
    }

    /**
     * @param $date
     * @param null $endDate
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDataByDate($date, $endDate = null)
    {
        $collection = $this->_orderFactory->create()->getCollection();
        $collection = $this->_helperData->addStoreFilter($collection);
        $collection = $this->_helperData->addStatusFilter($collection);
        $collection = $this->_helperData->addTimeFilter($collection, $date, $endDate);

        return $collection->getSize();
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRate()
    {
        $date         = $this->_helperData->getDateRange();
        $count        = $this->getDataByDate($date[0], $date[1]);
        $countCompare = $this->getDataByDate($date[2], $date[3]);
        if ($countCompare == 0 && $count == 0) {
            return 0;
        } else if ($countCompare == 0) {
            return 100;
        } else if ($count == 0) {
            return -100;
        }
        $rate = ($count - $countCompare) * 100 / $countCompare;

        return round($rate, 2);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTotal()
    {
        $date  = $this->_helperData->getDateRange();
        $count = $this->getDataByDate($date[0], $date[1]);

        return $count;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function getYLabel()
    {
        return __('Orders');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Orders');
    }
}