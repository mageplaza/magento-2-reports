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

namespace Mageplaza\Reports\Controller\Adminhtml\Cards;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
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
     * SavePosition constructor.
     * @param Context $context
     * @param Session $authSession
     * @param CardsManageFactory $cardsManageFactory
     */
    public function __construct(
        Context $context,
        Session $authSession,
        CardsManageFactory $cardsManageFactory
    )
    {
        parent::__construct($context);

        $this->_authSession        = $authSession;
        $this->_cardsManageFactory = $cardsManageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax() && $items = $this->getRequest()->getParam('items')) {
            $userId = $this->_authSession->getUser()->getId();
            $config = $this->_cardsManageFactory->getCurrentConfig();
            $data   = $config->getId()
                ? $config->getConfig()
                : $this->_cardsManageFactory->getDefaultConfig()->getConfig();
            foreach ($items as $id => $item) {
                $data[$id]['x']       = $item['x'];
                $data[$id]['y']       = $item['y'];
                $data[$id]['width']   = $item['width'];
                $data[$id]['height']  = $item['height'];
                $data[$id]['visible'] = isset($item['visible']) ? $item['visible'] : 1;
            }
            $data = \Mageplaza\Reports\Helper\Data::jsonEncode($data);
            if ($config->getId()) {
                $config->setConfig($data)->save();
            } else {
                $config->setData([
                    'namespace'  => 'mageplaza_reports_cards',
                    'user_id'    => $userId,
                    'identifier' => 'current',
                    'config'     => $data
                ])->save();
            }
        }
    }
}