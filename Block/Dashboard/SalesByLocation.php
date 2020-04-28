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
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class SalesByLocation
 * @package Mageplaza\Reports\Block\Dashboard
 */
class SalesByLocation extends AbstractClass
{
    const NAME = 'salesByLocation';

    /**
     * @var string
     */
    protected $_template = 'dashboard/sales_by_location.phtml';

    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var CountryFactory
     */
    protected $_countryFactory;

    /**
     * SalesByLocation constructor.
     *
     * @param Template\Context $context
     * @param CollectionFactory $orderCollectionFactory
     * @param CountryFactory $countryFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $orderCollectionFactory,
        CountryFactory $countryFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_countryFactory         = $countryFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context, $helperData, $data);
    }

    /**
     * @param      $startDate
     * @param      $endDate
     * @param null $size
     *
     * @return array
     * @throws LocalizedException*@throws \Exception
     * @throws Exception
     */
    protected function getDataByDateRange($startDate, $endDate, $size = null)
    {
        $data = [];
        /** @var Collection $collection */
        $collection = $this->_orderCollectionFactory->create();
        $collection = $this->_helperData->addStoreFilter($collection);
        $collection = $this->_helperData->addStatusFilter($collection);
        $collection = $this->_helperData->addTimeFilter($collection, $startDate, $endDate);
        $collection->getSelect()->join(
            ['soa' => $collection->getTable('sales_order_address')],
            "main_table.entity_id=soa.parent_id AND soa.address_type='billing'",
            ['country_count' => 'COUNT(country_id)', 'country_id']
        )->group('country_id')->order('country_count DESC');
        if ($size) {
            $collection->setPageSize($size);
        }

        foreach ($collection as $item) {
            $data[$item->getCountryId()] = ['count' => $item->getCountryCount()];
        }

        return $data;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    public function getCollection()
    {
        $collection  = [];
        $date        = $this->_helperData->getDateRange();
        $data        = $this->getDataByDateRange($date[0], $date[1], 5);
        $compareData = $this->getDataByDateRange($date[2], $date[3]);
        foreach ($data as $key => $item) {
            if (isset($compareData[$key]) && $compareData[$key] > 0) {
                $rate = ($item['count'] - $compareData[$key]['count']) / $compareData[$key]['count'];
            } else {
                $rate = 1;
            }
            $collection[] = [
                'country' => $this->getCountryNameById($key),
                'count'   => $item['count'],
                'rate'    => round($rate * 100, 2)
            ];
        }

        return $collection;
    }

    /**
     * @param $countryId
     *
     * @return string
     */
    private function getCountryNameById($countryId)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryId);

        return $country->getName();
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Sales By Location');
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return true;
    }
}
