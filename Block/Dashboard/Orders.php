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
use Magento\Sales\Model\OrderFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Orders
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
     *
     * @param Template\Context $context
     * @param OrderFactory $orderFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        OrderFactory $orderFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_orderFactory = $orderFactory;

        parent::__construct($context, $helperData, $data);
    }

    /**
     * @param      $date
     * @param null $endDate
     *
     * @return int
     * @throws LocalizedException*@throws \Exception
     * @throws Exception
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
     * @throws LocalizedException
     * @throws Exception
     */
    public function getRate()
    {
        $date         = $this->_helperData->getDateRange();
        $count        = $this->getDataByDate($date[0], $date[1]);
        $countCompare = $this->getDataByDate($date[2], $date[3]);
        if ($countCompare === 0 && $count === 0) {
            return 0;
        }
        if ($countCompare === 0) {
            return 100;
        }
        if ($count === 0) {
            return -100;
        }
        $rate = ($count - $countCompare) * 100 / $countCompare;

        return round($rate, 2);
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws Exception
     */
    public function getTotal()
    {
        $date = $this->_helperData->getDateRange();

        return $this->getDataByDate($date[0], $date[1]);
    }

    /**
     * @return Phrase|string
     */
    protected function getYLabel()
    {
        return __('Orders');
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Orders');
    }
}
