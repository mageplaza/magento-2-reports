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
use Mageplaza\Reports\Helper\Data;
use Mageplaza\ReportsPro\Model\ResourceModel\Viewed\CollectionFactory as ViewedCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

/**
 * Class ConversionFunnel
 * @package Mageplaza\Reports\Block\Dashboard
 */
class ConversionFunnel extends AbstractClass
{
    const NAME = 'conversionFunnel';

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Reports::dashboard/conversion_funnel.phtml';

    /**
     * @var ViewedCollectionFactory
     */
    protected $viewedCollectionFactory;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * ConversionFunnel constructor.
     * @param Template\Context $context
     * @param ViewedCollectionFactory $viewedCollectionFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ViewedCollectionFactory $viewedCollectionFactory,
        ItemCollectionFactory $itemCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Data $helperData,
        array $data = [])
    {
        parent::__construct($context, $helperData, $data);

        $this->viewedCollectionFactory    = $viewedCollectionFactory;
        $this->itemCollectionFactory      = $itemCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
    }

    /**
     * @return mixed
     */
    public function getProductViews()
    {
        $collection = $this->viewedCollectionFactory->create();
        if ($storeId = $this->getStore()) {
            $collection->addFieldToFilter('store_id', $storeId);
        }

        $dateRange = $this->getDateRange();
        if ($dateRange[0]) {
            $collection->addFieldToFilter('added_at', ['gteq' => $dateRange[0]]);
        }
        if ($dateRange[1]) {
            $collection->addFieldToFilter('added_at', ['lteq' => $dateRange[1]]);
        }

        return $collection->getSize();
    }

    /**
     * @return mixed
     */
    public function getAllCartItems()
    {
        $collection = $this->itemCollectionFactory->create();
        $collection = $this->addFilter($collection);

        return $collection->getSize();
    }

    /**
     * @return mixed
     */
    public function getAllOrderItem()
    {
        $collection = $this->orderItemCollectionFactory->create();
        $collection = $this->addFilter($collection);

        return $collection->getSize();
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\Collection|\Mageplaza\ReportsPro\Model\ResourceModel\Viewed\Collection|\Magento\Quote\Model\ResourceModel\Quote\Item\Collection| $collection
     * @return mixed
     */
    protected function addFilter($collection)
    {
        if ($storeId = $this->getStore()) {
            $collection->addFieldToFilter('store_id', $storeId);
        }

        $dateRange = $this->getDateRange();
        if ($dateRange[0]) {
            $collection->addFieldToFilter('created_at', ['gteq' => $dateRange[0]]);
        }
        if ($dateRange[1]) {
            $collection->addFieldToFilter('created_at', ['lteq' => $dateRange[1]]);
        }

        return $collection;
    }

    /**
     * @param null $from
     * @param null $to
     *
     * @return array
     * @throws \Exception
     */
    protected function getDateRange($from = null, $to = null)
    {
        if ($from === null) {
            $from = isset($this->_request->getParam('mpFilter')['startDate'])
                ? $this->_request->getParam('mpFilter')['startDate']
                : (($this->_request->getParam('startDate') !== null) ? $this->_request->getParam('startDate') : null);
        }
        if ($to === null) {
            $to = isset($this->_request->getParam('mpFilter')['endDate'])
                ? $this->_request->getParam('mpFilter')['endDate']
                : (($this->_request->getParam('endDate') !== null) ? $this->_request->getParam('endDate') : null);
        }
        if ($to == null || $from == null) {
            $dates = $this->_helperData->getDateRange();
            $from  = $dates[0];
            $to    = $dates[1];
        }

        return [$from, $to];
    }

    /**
     * @return int|mixed|null
     */
    protected function getStore()
    {
            $storeParam       = $this->_request->getParam('store');
            $storeFilterParam = isset($this->_request->getParam('mpFilter')['store'])
                ? $this->_request->getParam('mpFilter')['store'] : null;
            $storeId     = ($storeFilterParam !== null && $storeFilterParam !== "")
                ? $storeFilterParam
                : (($storeParam !== null && $storeParam !== "") ? $storeParam : 0);

        return $storeId;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Conversion Funnel');
    }
}