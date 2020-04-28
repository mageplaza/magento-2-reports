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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
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
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Data $helperData
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->helperData           = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $accessKey       = $this->getRequest()->getParam('accessKey');
        $accessKeyConfig = $this->helperData->getConfigMobileAccessKey();
        if ($accessKey !== $accessKeyConfig) {
            return $this->_redirect('noroute');
        }
        if ($this->getRequest()->isAjax()) {
            $dashBoard = $resultPage->getLayout()->getBlock('ar_dashboard');
            $result    = ['dashboard' => $dashBoard->toHtml()];

            return $this->getResponse()->representJson(Data::jsonEncode($result));
        }

        return $resultPage;
    }
}
