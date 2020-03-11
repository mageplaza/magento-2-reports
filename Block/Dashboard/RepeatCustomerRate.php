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
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Model\ResourceModel\Report\Order as AbstractReport;
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
     * @var AbstractReport
     */
    protected $abstractReport;

    /**
     * RepeatCustomerRate constructor.
     *
     * @param Template\Context $context
     * @param Data $helperData
     * @param AbstractReport $abstractReport
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        AbstractReport $abstractReport,
        array $data = []
    ) {
        $this->abstractReport = $abstractReport;

        parent::__construct($context, $helperData, $data);
    }

    /**
     * @param $startDate
     * @param $endDate
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getDataByDateRange($startDate, $endDate)
    {
        $data = [];

        $customerRepeatData = $this->getCustomerRepeatDataByDateRange($startDate, $endDate);

        while (strtotime($endDate) >= strtotime($startDate)) {
            $data['data']['labels'][]        = __('first');
            $data['compareData']['labels'][] = __('repeat');
            $data['data']['data'][]          = isset($customerRepeatData[$startDate])
                ? (int) $customerRepeatData[$startDate]['new_customer']
                : 0;
            $data['compareData']['data'][]   = isset($customerRepeatData[$startDate])
                ? (int) $customerRepeatData[$startDate]['repeat_customer']
                : 0;

            $startDate = strtotime('+1 day', strtotime($startDate));
            $startDate = date('Y-m-d', $startDate);
        }

        return $data;
    }

    /**
     * @param $startDate
     * @param $endDate
     *
     * @return array
     */
    protected function getCustomerRepeatDataByDateRange($startDate, $endDate)
    {
        $connection = $this->abstractReport->getConnection();
        $select     = $connection->select();

        $periodExpr = $connection->getDatePartSql(
            $this->abstractReport->getStoreTZOffsetQuery(
                ['source_table' => $this->abstractReport->getTable('sales_order')],
                'source_table.created_at',
                $startDate,
                $endDate
            )
        );

        $select->from(
            ['source_table' => $this->abstractReport->getTable('sales_order')],
            [
                'period'      => $periodExpr,
                'customer_id' => 'customer_id',
                'count'       => 'COUNT(customer_id)',
            ]
        );
        $select->where('state NOT IN (?)', ['pending_payment', 'new']);
        $select->where('status != ?', 'Canceled');
        $select->where('(customer_id > 0 OR customer_id IS NOT NULL)');
        $select->where($periodExpr . ' >= ?', $startDate);
        if ($endDate) {
            $select->where($periodExpr . ' <= ?', $endDate);
        }

        if ($storeId = $this->_request->getParam('store')) {
            $select->where('store_id = ?', $storeId);
        }

        $select->group(['period', 'customer_id']);

        $customerSelect = $connection->select();

        $customerSelect->from(
            ['sub_table' => $select],
            [
                'period'          => 'period',
                'new_customer'    => 'COUNT(CASE WHEN sub_table.count = 1 THEN 1 END)',
                'repeat_customer' => 'COUNT(CASE WHEN sub_table.count > 1 THEN 1 END)',
            ]
        );

        $customerSelect->group('period');
        $result = $connection->fetchAll($customerSelect);

        $data = [];
        foreach ($result as $value) {
            $data[$value['period']] = [
                'new_customer'    => $value['new_customer'],
                'repeat_customer' => $value['repeat_customer'],
            ];
        }

        return $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getChartData()
    {
        $data = [];
        try {
            $date = $this->_helperData->getDateRange();
        } catch (Exception $e) {
            $date = ['', ''];
        }
        $chartData           = $this->getDataByDateRange($date[0], $date[1]);
        $data['data']        = $chartData['data'];
        $data['compareData'] = $chartData['compareData'];
        $days                = $this->_helperData->getDaysByDateRange($date[0], $date[1]);
        $data['days']        = $days;
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
     *
     * @return float|int
     */
    private function getRepeatRateByDateRange($startDate, $endDate)
    {
        $customerRepeatData = $this->getCustomerRepeatDataByDateRange($startDate, $endDate);
        $newCustomer        = 0;
        $repeatCustomer     = 0;
        foreach ($customerRepeatData as $datum) {
            $newCustomer    += $datum['new_customer'];
            $repeatCustomer += $datum['repeat_customer'];
        }

        $total = $newCustomer + $repeatCustomer;

        return $total === 0 ? 0 : ($repeatCustomer / $total) * 100;
    }

    /**
     * @return int|string
     */
    public function getTotal()
    {
        $date  = $this->_helperData->getDateRange('Y-m-d');
        $total = $this->getRepeatRateByDateRange($date[0], $date[1]);

        return round($total, 2) . '%';
    }

    /**
     * @return float|int
     * @throws Exception
     */
    public function getRate()
    {
        $date              = $this->_helperData->getDateRange('Y-m-d');
        $repeatRate        = $this->getRepeatRateByDateRange($date[0], $date[1]);
        $compareRepeatRate = $this->getRepeatRateByDateRange($date[2], $date[3]);
        $rate              = $repeatRate - $compareRepeatRate;

        return round($rate, 2);
    }

    /**
     * @return Phrase|string
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
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Repeat Customer Rate');
    }
}
