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
use Magento\Backend\Block\Store\Switcher;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Reports\Helper\Data;

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
     * StoreFilter constructor.
     *
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if ($this->getRequest()->isAjax()) {
            $storeHtml = $resultPage->getLayout()
                ->createBlock(Switcher::class)
                ->setSwitchWebsites(0)
                ->setSwitchStoreGroups(0)
                ->setSwitchStoreViews(1)
                ->setUseConfirm(0)
                ->setDefaultSelectionName(__('All Websites'))
                ->toHtml();

            return $this->getResponse()->representJson(
                Data::jsonEncode(
                    ['store' => $storeHtml]
                )
            );
        }

        return $resultPage;
    }
}
