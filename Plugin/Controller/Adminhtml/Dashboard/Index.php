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

namespace Mageplaza\Reports\Plugin\Controller\Adminhtml\Dashboard;

use Magento\Backend\Controller\Adminhtml\Dashboard\Index as DashboardIndex;
use Magento\Framework\Json\Helper\Data;

/**
 * Class Index
 * @package Mageplaza\Reports\Plugin\Controller\Adminhtml\Dashboard
 */
class Index
{
    /**
     * @var Data
     */
    protected $_jsonHelper;

    /**
     * Index constructor.
     * @param Data $jsonHelper
     */
    public function __construct(Data $jsonHelper)
    {
        $this->_jsonHelper = $jsonHelper;
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Dashboard\Index $action
     * @param $page
     * @return mixed
     */
    public function afterExecute(DashboardIndex $action, $page)
    {
        if ($action->getRequest()->isAjax()) {
            $dashBoard = $page->getLayout()->getBlock('ar_dashboard');
            $result    = ['dashboard' => $dashBoard->toHtml()];
            $action->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
        } else {
            return $page;
        }
    }
}