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

namespace Mageplaza\Reports\Controller\Adminhtml\Details;

use Magento\Backend\App\Action;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class StoreFilter
 * @package Mageplaza\Reports\Controller\Adminhtml\Details
 */
class StoreFilter extends Action
{
    /**
     * @var bool|PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $_jsonHelper;

    /**
     * StoreFilter constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $jsonHelper
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_jsonHelper       = $jsonHelper;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if ($this->getRequest()->isAjax()) {
            $storeHtml = $resultPage->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher')
                ->setSwitchWebsites(0)
                ->setSwitchStoreGroups(0)
                ->setSwitchStoreViews(1)
                ->setUseConfirm(0)
                ->setDefaultSelectionName(__('All Websites'))
                ->toHtml();

            return $this->getResponse()->representJson(
                $this->_jsonHelper->jsonEncode(
                    ['store' => $storeHtml]
                ));
        }

        return $resultPage;
    }
}