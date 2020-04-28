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
use Magento\Framework\Phrase;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Model\ResourceModel\Viewed\Collection as ViewedCollection;
use Mageplaza\Reports\Model\ResourceModel\Viewed\CollectionFactory as ViewedCollectionFactory;

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
     *
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
        array $data = []
    ) {
        $this->viewedCollectionFactory    = $viewedCollectionFactory;
        $this->itemCollectionFactory      = $itemCollectionFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;

        parent::__construct($context, $helperData, $data);
    }

    /**
     * @return mixed
     * @throws Exception
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
     * @throws Exception
     */
    public function getAllCartItems()
    {
        $collection = $this->itemCollectionFactory->create();
        $collection = $this->addFilter($collection);

        return $collection->getSize();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getAllOrderItem()
    {
        $collection = $this->orderItemCollectionFactory->create();
        $collection = $this->addFilter($collection);

        return $collection->getSize();
    }

    /**
     * @param OrderItemCollection|ViewedCollection|Collection| $collection
     *
     * @return mixed
     * @throws Exception
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
     * @param null $fromDate
     * @param null $toDate
     *
     * @return array
     * @throws Exception
     */
    protected function getDateRange($fromDate = null, $toDate = null)
    {
        if ($fromDate === null) {
            $fromDate = isset($this->_request->getParam('mpFilter')['startDate'])
                ? $this->_request->getParam('mpFilter')['startDate']
                : $this->_request->getParam('startDate');
        }
        if ($toDate === null) {
            $toDate = isset($this->_request->getParam('mpFilter')['endDate'])
                ? $this->_request->getParam('mpFilter')['endDate']
                : $this->_request->getParam('endDate');
        }
        if ($toDate === null || $fromDate === null) {
            [$fromDate, $toDate] = $this->_helperData->getDateRange();
        }

        return [$fromDate, $toDate];
    }

    /**
     * @return int|mixed|null
     */
    protected function getStore()
    {
        $storeParam       = $this->_request->getParam('store') ?: 0;
        $storeFilterParam = isset($this->_request->getParam('mpFilter')['store'])
            ? $this->_request->getParam('mpFilter')['store'] : null;
        $storeId          = ($storeFilterParam !== null && $storeFilterParam !== '')
            ? $storeFilterParam
            : $storeParam;

        return $storeId;
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Conversion Funnel');
    }
}
