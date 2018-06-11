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

namespace Mageplaza\Reports\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Store\Model\Store;

/**
 * Class SwitchToNew
 * @package Mageplaza\Reports\Controller\Adminhtml\Dashboard
 */
class SwitchToNew extends Action
{
    /**
     * @var Writer
     */
    protected $_storageWriter;

    /**
     * @var TypeListInterface
     */
    protected $_cache;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * SwitchToNew constructor.
     * @param Context $context
     * @param Writer $storageWriter
     * @param TypeListInterface $typeList
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        Writer $storageWriter,
        TypeListInterface $typeList,
        JsonHelper $jsonHelper
    )
    {
        parent::__construct($context);

        $this->_storageWriter = $storageWriter;
        $this->_cache         = $typeList;
        $this->_jsonHelper    = $jsonHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|null
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            if ($this->getRequest()->getParam('switchToNew')) {
                $this->saveConfig('mageplaza_reports/general/enabled', 1);
                $this->getResponse()->representJson(
                    $this->_jsonHelper->jsonEncode($this->getUrl('admin/dashboard/index'))
                );
            }
            if ($this->getRequest()->getParam('firstTimeInstall')) {
                $this->saveConfig('mageplaza_reports/general/first_time_install', 0);

                return null;
            }
            $this->saveConfig('mageplaza_reports/general/shownof', 0);

            return null;
        }
        $this->saveConfig('mageplaza_reports/general/enabled', 1);

        return $this->resultRedirectFactory->create()->setPath('admin/dashboard/index');
    }

    /**
     * @param $code
     * @param $value
     */
    private function saveConfig($code, $value)
    {
        $this->_storageWriter->save($code,
            $value,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID);
        $this->_cache->cleanType('config');
        $this->_cache->cleanType('full_page');
    }
}