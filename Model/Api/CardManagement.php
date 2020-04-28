<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

namespace Mageplaza\Reports\Model\Api;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Reports\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestsellersCollectionFactory;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueriesCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Reports\Api\CardManagementInterface;
use Mageplaza\Reports\Block\Dashboard\AverageOrder;
use Mageplaza\Reports\Block\Dashboard\AverageOrderValue;
use Mageplaza\Reports\Block\Dashboard\ConversionFunnel;
use Mageplaza\Reports\Block\Dashboard\LifetimeSales;
use Mageplaza\Reports\Block\Dashboard\Orders;
use Mageplaza\Reports\Block\Dashboard\RepeatCustomerRate;
use Mageplaza\Reports\Block\Dashboard\SalesByLocation;
use Mageplaza\Reports\Block\Dashboard\Shipping;
use Mageplaza\Reports\Block\Dashboard\Tax;
use Mageplaza\Reports\Block\Dashboard\TotalSales;
use Mageplaza\Reports\Helper\Data as HelperData;
use Mageplaza\Reports\Model\Api\Data\Card;

/**
 * Class CardManagement
 * @package Mageplaza\Reports\Model\Api
 */
class CardManagement implements CardManagementInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var AverageOrder
     */
    protected $averageOrderCard;

    /**
     * @var Card
     */
    protected $card;

    /**
     * @var AverageOrderValue
     */
    protected $averageOrderValue;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var BestsellersCollectionFactory
     */
    protected $bestsellersCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ConversionFunnel
     */
    protected $conversionFunnel;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var QueriesCollectionFactory
     */
    protected $queriesFactory;

    /**
     * @var LifetimeSales
     */
    protected $lifetimeSales;

    /**
     * @var ProductCollectionFactory
     */
    protected $productsFactory;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var Orders
     */
    protected $orders;

    /**
     * @var RepeatCustomerRate
     */
    protected $repeatCustomerRate;

    /**
     * @var SalesByLocation
     */
    protected $salesByLocation;

    /**
     * @var Shipping
     */
    protected $shipping;

    /**
     * @var Tax
     */
    protected $tax;

    /**
     * @var TotalSales
     */
    protected $totalSales;

    /**
     * CardManagement constructor.
     *
     * @param RequestInterface $request
     * @param HelperData $helperData
     * @param Card $card
     * @param AverageOrder $averageOrderCard
     * @param AverageOrderValue $averageOrderValue
     * @param StoreManagerInterface $storeManager
     * @param BestsellersCollectionFactory $bestsellersCollectionFactory
     * @param ConversionFunnel $conversionFunnel
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param QueriesCollectionFactory $queriesFactory
     * @param LifetimeSales $lifetimeSales
     * @param ProductCollectionFactory $productsFactory
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param Orders $orders
     * @param RepeatCustomerRate $repeatCustomerRate
     * @param SalesByLocation $salesByLocation
     * @param Shipping $shipping
     * @param Tax $tax
     * @param TotalSales $totalSales
     */
    public function __construct(
        RequestInterface $request,
        HelperData $helperData,
        Card $card,
        AverageOrder $averageOrderCard,
        AverageOrderValue $averageOrderValue,
        StoreManagerInterface $storeManager,
        BestsellersCollectionFactory $bestsellersCollectionFactory,
        ConversionFunnel $conversionFunnel,
        OrderCollectionFactory $orderCollectionFactory,
        QueriesCollectionFactory $queriesFactory,
        LifetimeSales $lifetimeSales,
        ProductCollectionFactory $productsFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        Orders $orders,
        RepeatCustomerRate $repeatCustomerRate,
        SalesByLocation $salesByLocation,
        Shipping $shipping,
        Tax $tax,
        TotalSales $totalSales
    ) {
        $this->helperData                   = $helperData;
        $this->averageOrderCard             = $averageOrderCard;
        $this->card                         = $card;
        $this->averageOrderValue            = $averageOrderValue;
        $this->request                      = $request;
        $this->bestsellersCollectionFactory = $bestsellersCollectionFactory;
        $this->storeManager                 = $storeManager;
        $this->conversionFunnel             = $conversionFunnel;
        $this->orderCollectionFactory       = $orderCollectionFactory;
        $this->queriesFactory               = $queriesFactory;
        $this->lifetimeSales                = $lifetimeSales;
        $this->productsFactory              = $productsFactory;
        $this->customerCollectionFactory    = $customerCollectionFactory;
        $this->orders                       = $orders;
        $this->repeatCustomerRate           = $repeatCustomerRate;
        $this->salesByLocation              = $salesByLocation;
        $this->shipping                     = $shipping;
        $this->tax                          = $tax;
        $this->totalSales                   = $totalSales;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function get($cardName)
    {
        $filters = $this->addFilter();

        return $this->getCard($cardName, $filters);
    }

    /**
     * @param $cardName
     * @param $filters
     *
     * @return Card|null
     * @throws LocalizedException
     */
    protected function getCard($cardName, $filters)
    {
        [$storeFilter, $storeId, $storeIds] = $filters;

        $result = new Card();
        $result->setName($cardName);
        switch ($cardName) {
            case 'averageOrder':
                $result->setData(['total' => $this->averageOrderCard->getTotal(false)]);
                break;
            case 'averageOrderValue':
                $result->setData([
                    'total'     => $this->averageOrderValue->getTotal(false),
                    'rate'      => $this->averageOrderValue->getRate(),
                    'chartData' => $this->averageOrderValue->getChartData(),
                ]);
                break;
            case 'bestsellers':
                $collection = $this->bestsellersCollectionFactory->create()->setModel(
                    Product::class
                );
                if ($storeFilter !== null) {
                    $collection->addStoreFilter($storeId);
                }
                $result->setData($collection->load()->getData());
                break;
            case 'conversionFunnel':
                $data = [
                    'product_viewed' => $this->conversionFunnel->getProductViews(),
                    'add_to_cart'    => $this->conversionFunnel->getAllCartItems(),
                    'ordered'        => $this->conversionFunnel->getAllOrderItem(),
                ];
                $result->setData($data);
                break;
            case 'customers':
                $collection = $this->orderCollectionFactory->create();
                /* @var $collection Collection */
                $collection->groupByCustomer()->addOrdersCount()->joinCustomerName();
                if ($storeFilter !== null) {
                    $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
                }
                $collection->addSumAvgTotals($storeFilter)->orderByTotalAmount();
                $collection->load();
                $result->setData($collection->getData());
                break;
            case 'lastOrders':
                $collection = $this->orderCollectionFactory->create()->addItemCountExpr()->joinCustomerName('customer')
                    ->orderByCreatedAt();
                if ($storeFilter !== null) {
                    $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
                    $collection->addRevenueToSelect();
                } else {
                    $collection->addRevenueToSelect(true);
                }
                $collection->setPageSize(5)->load();
                $result->setData($collection->getData());
                break;
            case 'lastSearches':
                $collection = $this->queriesFactory->create();
                $collection->setRecentQueryFilter();
                if ($storeFilter !== null) {
                    $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
                }
                $collection->setPageSize(5)->load();
                $result->setData($collection->getData());
                break;
            case 'lifetimeSales':
                $result->setData(['total' => $this->lifetimeSales->getTotal(false)]);
                break;
            case 'mostViewedProducts':
                $collection = $this->productsFactory->create()->addAttributeToSelect(
                    '*'
                )->addViewsCount();
                if ($storeFilter !== null) {
                    $collection->addViewsCount()->setStoreId($storeId)->addStoreFilter($storeId);
                }
                $collection->setPageSize(5)->load();
                $data = [];
                foreach ($collection as $item) {
                    $data[] = $item->getData();
                }
                $result->setData($data);
                break;
            case 'newCustomers':
                $collection = $this->customerCollectionFactory->create()->addCustomerName();
                if ($storeFilter !== null) {
                    $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
                }
                $collection->addOrdersStatistics($storeFilter)->orderByCustomerRegistration();
                $collection->setPageSize(5)->load();
                $result->setData($collection->getData());
                break;
            case 'orders':
                $result->setData([
                    'total'     => $this->orders->getTotal(),
                    'rate'      => $this->orders->getRate(),
                    'chartData' => $this->orders->getChartData(),
                ]);
                break;
            case 'repeatCustomerRate':
                $result->setData([
                    'total'     => $this->repeatCustomerRate->getTotal(),
                    'rate'      => $this->repeatCustomerRate->getRate(),
                    'chartData' => $this->repeatCustomerRate->getChartData()
                ]);
                break;
            case 'salesByLocation':
                $collection = $this->salesByLocation->getCollection();
                $result->setData(['items' => $collection, 'chartData' => $this->salesByLocation->getChartData()]);
                break;
            case 'shipping':
                $result->setData([
                    'total'     => $this->shipping->getTotal(false),
                    'rate'      => $this->shipping->getRate(),
                    'chartData' => $this->shipping->getChartData()
                ]);
                break;
            case 'tax':
                $result->setData([
                    'total'     => $this->tax->getTotal(false),
                    'rate'      => $this->tax->getRate(),
                    'chartData' => $this->tax->getChartData()
                ]);
                break;
            case 'topSearches':
                $collection = $this->queriesFactory->create();
                if ($storeFilter !== null) {
                    $collection->setPopularQueryFilter($storeIds);
                } else {
                    $collection->setOrder('popularity', 'DESC');
                }
                $collection->setPageSize(5)->load();
                $result->setData($collection->getData());
                break;
            case 'totalSales':
                $result->setData([
                    'total'     => $this->totalSales->getTotal(false),
                    'rate'      => $this->totalSales->getRate(),
                    'chartData' => $this->totalSales->getChartData()
                ]);
                break;
            default:
                $result = null;
        }

        return $result;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function addFilter()
    {
        $params = $this->request->getParams();
        if (isset($params['startDate'])) {
            $params['dateRange'][0] = $params['startDate'];
            $params['dateRange'][2] = isset($params['compareStartDate']) ? $params['compareStartDate'] : null;
        }
        if (isset($params['endDate'])) {
            $params['dateRange'][1] = $params['endDate'];
            $params['dateRange'][3] = isset($params['compareEndDate']) ? $params['compareEndDate'] : null;
        }
        $storeFilter = 0;
        $storeId     = null;
        $storeIds    = null;
        if ($this->getParam('website')) {
            $storeIds = $this->storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId  = array_pop($storeIds);
        } elseif ($this->getParam('store')) {
            $storeId     = (int) $this->getParam('store');
            $storeIds    = [$storeId];
            $storeFilter = 1;
        } else {
            $storeFilter = null;
        }

        $this->request->setParams($params);

        return [$storeFilter, $storeId, $storeIds];
    }

    /**
     * @param $key
     * @param null $defaultValue
     *
     * @return mixed
     */
    protected function getParam($key, $defaultValue = null)
    {
        return $this->request->getParam($key, $defaultValue);
    }
}
