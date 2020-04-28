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

use Exception;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Ui\Model\Bookmark;
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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

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
     *
     * @param Session $authSession
     * @param ObjectManager $objectManager
     * @param ManagerInterface $eventManager
     * @param BookmarkFactory $bookmark
     * @param Data $helperData
     * @param array $map
     * @param array $charts
     */
    public function __construct(
        Session $authSession,
        ObjectManager $objectManager,
        ManagerInterface $eventManager,
        BookmarkFactory $bookmark,
        Data $helperData,
        array $map = [],
        array $charts = []
    ) {
        $this->_authSession  = $authSession;
        $this->objectManager = $objectManager;
        $this->eventManager  = $eventManager;
        $this->_bookmark     = $bookmark;
        $this->_helperData   = $helperData;
        $this->_map          = $map;
        $this->_charts       = $charts;
    }

    /** create Cards Collection
     *
     * @return array
     * @throws Exception
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
            $block->setCardId($alias);
            if (isset($config[$alias])) {
                $block->addData([
                    'x'       => $config[$alias]['x'],
                    'y'       => $config[$alias]['y'],
                    'width'   => $config[$alias]['width'],
                    'height'  => $config[$alias]['height'],
                    'visible' => isset($config[$alias]['visible']) ? $config[$alias]['visible'] : 1
                ]);
            }

            $cards[$alias] = $block;
        }

        return $cards;
    }

    /**
     * @return DataObject|Bookmark
     * @throws Exception
     */
    public function getCurrentConfig()
    {
        if ($this->_authSession->getUser()) {
            $userId  = $this->_authSession->getUser()->getId();
            $current = $this->_bookmark->create()->getCollection()
                ->addFieldToFilter('namespace', 'mageplaza_reports_cards')
                ->addFieldToFilter('user_id', $userId)
                ->addFieldToFilter('identifier', 'current')->getFirstItem();
        } else {
            $userId  = '1';
            $current = $this->_bookmark->create()->getCollection()
                ->addFieldToFilter('namespace', 'mageplaza_reports_cards_mobile')
                ->addFieldToFilter('user_id', $userId)
                ->addFieldToFilter('identifier', 'current')->getFirstItem();
        }

        return $current;
    }

    /**
     * @return DataObject|Bookmark
     * @throws Exception
     */
    public function getDefaultConfig()
    {
        /** @var Bookmark $default */
        if ($this->_authSession->getUser()) {
            $userId  = $this->_authSession->getUser()->getId();
            $default = $this->_bookmark->create()->getCollection()
                ->addFieldToFilter('namespace', 'mageplaza_reports_cards')
                ->addFieldToFilter('user_id', $userId)
                ->addFieldToFilter('identifier', 'default')->getFirstItem();
            if (!$default->getId()) {
                $default = $this->_bookmark->create()->addData([
                    'namespace'  => 'mageplaza_reports_cards',
                    'identifier' => 'default',
                    'user_id'    => $userId,
                    'config'     => '{"lastOrders":{"x":"0","y":"6","width":"3","height":"10","visible":1},"totals":{"x":3,"y":0,"width":3,"height":4,"visible":1},"sales":{"x":6,"y":0,"width":3,"height":4,"visible":1},"grids":{"x":"0","y":"12","width":"5","height":"4","visible":1},"diagrams":{"x":0,"y":4,"width":3,"height":4,"visible":1},"lastSearches":{"x":"0","y":"16","width":"3","height":"3","visible":1},"topSearches":{"x":"0","y":"19","width":"3","height":"3","visible":1},"transactions":{"x":9,"y":4,"width":3,"height":4,"visible":1},"averageOrderValue":{"x":"100","y":"100","width":"3","height":"10","visible":0},"salesByLocation":{"x":"9","y":"0","width":"3","height":"10","visible":1},"repeatCustomerRate":{"x":"3","y":"28","width":"6","height":"14","visible":1},"totalSales":{"x":"3","y":"0","width":"6","height":"14","visible":1},"orders":{"x":"3","y":"14","width":"6","height":"14","visible":1},"bestsellers":{"x":"9","y":"10","width":"3","height":"10","visible":1},"customers":{"x":"9","y":"30","width":"3","height":"10","visible":1},"lifetimeSales":{"x":"0","y":"0","width":"3","height":"3","visible":1},"shipping":{"x":"100","y":"100","width":"4","height":"10","visible":0},"newCustomers":{"x":"9","y":"20","width":"3","height":"10","visible":1},"mostViewedProducts":{"x":"9","y":"40","width":"3","height":"10","visible":1},"tax":{"x":"100","y":"100","width":"3","height":"9","visible":0},"averageOrder":{"x":"0","y":"3","width":"3","height":"3","visible":1}}'
                ])->save();
            }
        } else {
            $default = $this->_bookmark->create()->getCollection()
                ->addFieldToFilter('namespace', 'mageplaza_reports_cards_mobile')
                ->addFieldToFilter('user_id', '1')
                ->addFieldToFilter('identifier', 'default')->getFirstItem();
            if (!$default->getId()) {
                $default = $this->_bookmark->create()->addData([
                    'namespace'  => 'mageplaza_reports_cards_mobile',
                    'identifier' => 'default',
                    'user_id'    => '1',
                    'config'     => '{"lastOrders":{"x":"0","y":"6","width":"3","height":"10","visible":1},"totals":{"x":3,"y":0,"width":3,"height":4,"visible":1},"sales":{"x":6,"y":0,"width":3,"height":4,"visible":1},"grids":{"x":"0","y":"12","width":"5","height":"4","visible":1},"diagrams":{"x":0,"y":4,"width":3,"height":4,"visible":1},"lastSearches":{"x":"0","y":"16","width":"3","height":"3","visible":1},"topSearches":{"x":"0","y":"19","width":"3","height":"3","visible":1},"transactions":{"x":9,"y":4,"width":3,"height":4,"visible":1},"averageOrderValue":{"x":"100","y":"100","width":"3","height":"10","visible":0},"salesByLocation":{"x":"9","y":"0","width":"3","height":"10","visible":1},"repeatCustomerRate":{"x":"3","y":"28","width":"6","height":"14","visible":1},"totalSales":{"x":"3","y":"0","width":"6","height":"14","visible":1},"orders":{"x":"3","y":"14","width":"6","height":"14","visible":1},"bestsellers":{"x":"9","y":"10","width":"3","height":"10","visible":1},"customers":{"x":"9","y":"30","width":"3","height":"10","visible":1},"lifetimeSales":{"x":"0","y":"0","width":"3","height":"3","visible":1},"shipping":{"x":"100","y":"100","width":"4","height":"10","visible":0},"newCustomers":{"x":"9","y":"20","width":"3","height":"10","visible":1},"mostViewedProducts":{"x":"9","y":"40","width":"3","height":"10","visible":1},"tax":{"x":"100","y":"100","width":"3","height":"9","visible":0},"averageOrder":{"x":"0","y":"3","width":"3","height":"3","visible":1}}'
                ])->save();
            }
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        $cards = new DataObject($this->_map);

        $this->eventManager->dispatch('mageplaza_report_init_cards', ['cards' => $cards]);

        return $cards->getData();
    }
}
