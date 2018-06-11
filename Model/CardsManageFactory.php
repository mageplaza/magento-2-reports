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

namespace Mageplaza\Reports\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Ui\Model\BookmarkFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Cards
 * @package Mageplaza\Reports\Model
 */
class CardsManageFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var array
     */
    protected $_map;

    /**
     * @var array
     */
    protected $_charts;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var BookmarkFactory
     */
    protected $_bookmark;

    /**
     * @var Session
     */
    protected $_authSession;

    /**
     * CardsManageFactory constructor.
     * @param Session $authSession
     * @param ObjectManager $objectManager
     * @param BookmarkFactory $bookmark
     * @param Data $helperData
     * @param array $map
     * @param array $charts
     */
    public function __construct(
        Session $authSession,
        ObjectManager $objectManager,
        BookmarkFactory $bookmark,
        Data $helperData,
        array $map = [],
        array $charts = []
    )
    {
        $this->_authSession = $authSession;
        $this->objectManager = $objectManager;
        $this->_bookmark     = $bookmark;
        $this->_helperData   = $helperData;
        $this->_map          = $map;
        $this->_charts       = $charts;
    }

    /** create Cards Collection
     *
     * @return array
     * @throws \Exception
     */
    public function create()
    {
        $map = $this->getMap();

        $config = $this->getCurrentConfig()->getId()
            ? $this->getCurrentConfig()->getConfig()
            : $this->getDefaultConfig()->getConfig();

        $cards = [];

        foreach ($map as $alias => $blockInstanceName) {
            $block = $this->objectManager->create($blockInstanceName);
            if (isset($config[$alias])) {
                $card = new DataObject([
                    'id'             => $alias,
                    'data_gs_x'      => $config[$alias]['data_gs_x'],
                    'data_gs_y'      => $config[$alias]['data_gs_y'],
                    'data_gs_width'  => $config[$alias]['data_gs_width'],
                    'data_gs_height' => $config[$alias]['data_gs_height'],
                    'visible'        => isset($config[$alias]['visible']) ? $config[$alias]['visible'] : 1,
                ]);
            } else {
                $card = new DataObject();
                $card->setId($alias);
            }

            $card->setTitle($block->getTitle());
            $cards[$alias] = $card;
        }

        return $cards;
    }

    /**
     * @return \Magento\Framework\DataObject|\Magento\Ui\Model\Bookmark
     * @throws \Exception
     */
    public function getCurrentConfig()
    {
        $userId = $this->_authSession->getUser()->getId();

        $current = $this->_bookmark->create()->getCollection()
            ->addFieldToFilter('namespace', 'mageplaza_reports_cards')
            ->addFieldToFilter('user_id', $userId)
            ->addFieldToFilter('identifier', 'current')->getFirstItem();

        return $current;
    }

    /**
     * @return DataObject|\Magento\Ui\Model\Bookmark
     * @throws \Exception
     */
    public function getDefaultConfig()
    {
        $userId = $this->_authSession->getUser()->getId();

        $default = $this->_bookmark->create()->getCollection()
            ->addFieldToFilter('namespace', 'mageplaza_reports_cards')
            ->addFieldToFilter('user_id', $userId)
            ->addFieldToFilter('identifier', 'default')->getFirstItem();
        if (!$default->getId()) {
//            $defaultConfig = [
//                'lastOrders'         => ['data_gs_x' => 0, 'data_gs_y' => 0, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'totals'             => ['data_gs_x' => 3, 'data_gs_y' => 0, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'sales'              => ['data_gs_x' => 6, 'data_gs_y' => 0, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'grids'              => ['data_gs_x' => 9, 'data_gs_y' => 0, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'diagrams'           => ['data_gs_x' => 0, 'data_gs_y' => 4, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'lastSearches'       => ['data_gs_x' => 3, 'data_gs_y' => 4, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'topSearches'        => ['data_gs_x' => 6, 'data_gs_y' => 4, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'transactions'       => ['data_gs_x' => 9, 'data_gs_y' => 4, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'averageOrderValue'  => ['data_gs_x' => 0, 'data_gs_y' => 8, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'salesByLocation'    => ['data_gs_x' => 3, 'data_gs_y' => 8, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'repeatCustomerRate' => ['data_gs_x' => 6, 'data_gs_y' => 8, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//                'totalSales'         => ['data_gs_x' => 9, 'data_gs_y' => 8, 'data_gs_width' => 3, 'data_gs_height' => 4, 'visible' => 1],
//            ];

            $default = $this->_bookmark->create()->addData([
                'namespace'  => 'mageplaza_reports_cards',
                'identifier' => 'default',
                'user_id'    => $userId,
                'config'     => '{"lastOrders":{"data_gs_x":"0","data_gs_y":"6","data_gs_width":"3","data_gs_height":"10","visible":1},"totals":{"data_gs_x":3,"data_gs_y":0,"data_gs_width":3,"data_gs_height":4,"visible":1},"sales":{"data_gs_x":6,"data_gs_y":0,"data_gs_width":3,"data_gs_height":4,"visible":1},"grids":{"data_gs_x":"0","data_gs_y":"12","data_gs_width":"5","data_gs_height":"4","visible":1},"diagrams":{"data_gs_x":0,"data_gs_y":4,"data_gs_width":3,"data_gs_height":4,"visible":1},"lastSearches":{"data_gs_x":"0","data_gs_y":"16","data_gs_width":"3","data_gs_height":"3","visible":1},"topSearches":{"data_gs_x":"0","data_gs_y":"19","data_gs_width":"3","data_gs_height":"3","visible":1},"transactions":{"data_gs_x":9,"data_gs_y":4,"data_gs_width":3,"data_gs_height":4,"visible":1},"averageOrderValue":{"data_gs_x":"100","data_gs_y":"100","data_gs_width":"3","data_gs_height":"10","visible":"0"},"salesByLocation":{"data_gs_x":"9","data_gs_y":"0","data_gs_width":"3","data_gs_height":"10","visible":1},"repeatCustomerRate":{"data_gs_x":"3","data_gs_y":"28","data_gs_width":"6","data_gs_height":"14","visible":1},"totalSales":{"data_gs_x":"3","data_gs_y":"0","data_gs_width":"6","data_gs_height":"14","visible":1},"orders":{"data_gs_x":"3","data_gs_y":"14","data_gs_width":"6","data_gs_height":"14","visible":1},"bestsellers":{"data_gs_x":"9","data_gs_y":"10","data_gs_width":"3","data_gs_height":"10","visible":1},"customers":{"data_gs_x":"9","data_gs_y":"30","data_gs_width":"3","data_gs_height":"10","visible":1},"lifetimeSales":{"data_gs_x":"0","data_gs_y":"0","data_gs_width":"3","data_gs_height":"3","visible":1},"shipping":{"data_gs_x":"100","data_gs_y":"100","data_gs_width":"4","data_gs_height":"10","visible":"0"},"newCustomers":{"data_gs_x":"9","data_gs_y":"20","data_gs_width":"3","data_gs_height":"10","visible":1},"mostViewedProducts":{"data_gs_x":"9","data_gs_y":"40","data_gs_width":"3","data_gs_height":"10","visible":1},"tax":{"data_gs_x":"100","data_gs_y":"100","data_gs_width":"3","data_gs_height":"9","visible":"0"},"averageOrder":{"data_gs_x":"0","data_gs_y":"3","data_gs_width":"3","data_gs_height":"3","visible":1}}'
            ])->save();
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->_map;
    }
}
