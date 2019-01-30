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

namespace Mageplaza\Reports\Controller\Dashboard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class StoreFilter
 * @package Mageplaza\Reports\Controller\Adminhtml\Details
 */
class Index extends Action
{
    /**
     * @var bool|PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Data
     */
    protected $_jsonHelper;

    /**
     * @var \Mageplaza\Reports\Helper\Data
     */
    protected $helperData;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Mageplaza\Reports\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        JsonHelper $jsonHelper,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->helperData = $helperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $accessKey = $this->getRequest()->getParam('accessKey');
        $accessKeyConfig = $this->helperData->getConfigMobileAccessKey();
        if ($accessKey !== $accessKeyConfig) {
            return $this->_redirect('noroute');
        }
        if ($this->getRequest()->isAjax()) {
            $dashBoard = $resultPage->getLayout()->getBlock('ar_dashboard');
            $result = ['dashboard' => $dashBoard->toHtml()];

            return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
        }

        return $resultPage;
    }
}
