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
 * Class RepeatCustomerRate
 * @package Mageplaza\Reports\Block\Dashboard
 */
class RepeatCustomerRate extends AbstractClass
{
    const NAME = 'repeatCustomerRate';

    /**
     * @var string
     */
    protected $_template = 'dashboard/chart.phtml';

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var
     */
    protected $_orderReportCollectionFactory;

    /**
     * RepeatCustomerRate constructor.
     * @param Template\Context $context
     * @param Data $helperData
     * @param OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        OrderFactory $orderFactory,
        array $data = []
    )
    {
        parent::__construct($context, $helperData, $data);

        $this->_orderFactory = $orderFactory;
    }

    /**
     * @param $customerId
     * @param $time
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkRepeatCustomer($customerId, $time)
    {
        $collection = $this->_orderFactory->create()->getCollection();
        $collection = $this->_helperData->addStoreFilter($collection);
        $collection = $this->_helperData->addStatusFilter($collection);
        $collection->addFieldToFilter('created_at', ['lt' => $time]);

        return $collection->addFieldToFilter('customer_id', $customerId)->getSize();
    }

    /**
     * @param $startDate
     * @param null $endDate
     * @return array|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDataByDate($startDate, $endDate = null)
    {
        $customerOrdersPerTime = $this->_orderFactory->create()->getCollection();
        $customerOrdersPerTime = $this->_helperData->addStoreFilter($customerOrdersPerTime);
        $customerOrdersPerTime = $this->_helperData->addStatusFilter($customerOrdersPerTime);
        $customerOrdersPerTime = $this->_helperData->addTimeFilter($customerOrdersPerTime, $startDate, $endDate);
        $customerOrdersPerTime->addFieldToFilter('customer_is_guest', 0);

        $orders                  = $this->_orderFactory->create()->getCollection();
        $orders                  = $this->_helperData->addStoreFilter($orders);
        $orders                  = $this->_helperData->addStatusFilter($orders);
        $orders                  = $this->_helperData->addTimeFilter($orders, $startDate, $endDate);
        $guestOrdersPerTimeCount = $orders->addFieldToFilter('customer_is_guest', 1)->getSize();

        $first  = 0;
        $repeat = [];
        foreach ($customerOrdersPerTime as $order) {
            if ($this->checkRepeatCustomer($order->getCustomerId(), $order->getCreatedAt())) {
                $repeat[$order->getCustomerId()] = 1;
            } else {
                $first++;
            }
        }

        return [$first + $guestOrdersPerTimeCount, count($repeat)];
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDataByDateRange($startDate, $endDate)
    {
        $data = [];
        while (strtotime($endDate) >= strtotime($startDate)) {
            $data['data']['labels'][]        = __('first');
            $data['compareData']['labels'][] = __('repeat');
            $customerRepeat                  = $this->getDataByDate($startDate);
            $data['data']['data'][]          = $customerRepeat[0];
            $data['compareData']['data'][]   = $customerRepeat[1];
            $startDate                       = strtotime('+1 day', strtotime($startDate));
            $startDate                       = date('Y-m-d H:i:s', $startDate);
        }

        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getChartData()
    {
        $data                = [];
        $date                = $this->_helperData->getDateRange();
        $data['data']        = $this->getDataByDateRange($date[0], $date[1])['data'];
        $data['compareData'] = $this->getDataByDateRange($date[0], $date[1])['compareData'];
        $data['days']        = $days = $this->_helperData->getDaysByDateRange($date[0], $date[1]);
        $data['labels']      = $this->_helperData->getPeriodsDate($date[0], null, $days);
        $data['stepSize']    = round($days / 6);
        $data['total']       = $this->getTotal();
        $data['rate']        = $this->getRate();
        $data['label']       = $this->getChartDataLabel();
        $data['yUnit']       = $this->getYUnit();
        $data['yLabel']      = $this->getYLabel();
        $data['isFill']      = $this->isFill();
        $data['isCompare']   = $this->isCompare();
        $data['name']        = $this->getName();

        return $data;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRepeatRateByDateRange($startDate, $endDate)
    {
        $customerRepeat = $this->getDataByDate($startDate, $endDate);
        if (($customerRepeat[1] + $customerRepeat[0]) == 0) {
            return 0;
        }

        return ($customerRepeat[1] / ($customerRepeat[1] + $customerRepeat[0])) * 100;
    }

    /**
     * @return int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTotal()
    {
        $date  = $this->_helperData->getDateRange();
        $total = $this->getRepeatRateByDateRange($date[0], $date[1]);

        return round($total, 2) . '%';
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRate()
    {
        $date              = $this->_helperData->getDateRange();
        $repeatRate        = $this->getRepeatRateByDateRange($date[0], $date[1]);
        $compareRepeatRate = $this->getRepeatRateByDateRange($date[2], $date[3]);
        $rate              = $repeatRate - $compareRepeatRate;

        return round($rate, 2);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function getYLabel()
    {
        return __('Customers');
    }

    /**
     * @return bool
     */
    protected function isFill()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getChartDataLabel()
    {
        return [__('first'), __('repeat')];
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Repeat Customer Rate');
    }
}