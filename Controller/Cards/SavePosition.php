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

namespace Mageplaza\Reports\Controller\Cards;

use Exception;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Model\CardsManageFactory;

/**
 * Class SavePosition
 * @package Mageplaza\Reports\Controller\Adminhtml\Cards
 */
class SavePosition extends Action
{
    /**
     * @var Session
     */
    protected $_authSession;

    /**
     * @var CardsManageFactory
     */
    protected $_cardsManageFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * SavePosition constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param CardsManageFactory $cardsManageFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Session $authSession,
        CardsManageFactory $cardsManageFactory,
        Data $helperData
    ) {
        $this->_authSession        = $authSession;
        $this->_cardsManageFactory = $cardsManageFactory;
        $this->helperData          = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $items = $this->getRequest()->getParam('items');
        if ($items
            && $this->getRequest()->isAjax()
            && $this->helperData->isValidMobileAccessKey($this->getRequest()->getParam('accessKey'))
        ) {
            $config = $this->_cardsManageFactory->getCurrentConfig();
            $data   = $config->getId()
                ? $config->getConfig()
                : $this->_cardsManageFactory->getDefaultConfig()->getConfig();
            foreach ($items as $id => $item) {
                $data[$id]['x']       = (int) $item['x'];
                $data[$id]['y']       = (int) $item['y'];
                $data[$id]['width']   = (int) $item['width'];
                $data[$id]['height']  = (int) $item['height'];
                $data[$id]['visible'] = isset($item['visible']) ? (int) $item['visible'] : 1;
            }
            $data = Data::jsonEncode($data);
            if ($config->getId()) {
                $config->setConfig($data)->save();
            } else {
                $config->setData([
                    'namespace'  => 'mageplaza_reports_cards_mobile',
                    'user_id'    => '1',
                    'identifier' => 'current',
                    'config'     => $data
                ])->save();
            }
        }
    }
}
