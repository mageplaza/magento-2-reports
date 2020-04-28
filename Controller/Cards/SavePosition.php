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
     * SavePosition constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param CardsManageFactory $cardsManageFactory
     */
    public function __construct(
        Context $context,
        Session $authSession,
        CardsManageFactory $cardsManageFactory
    ) {
        $this->_authSession        = $authSession;
        $this->_cardsManageFactory = $cardsManageFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $items = $this->getRequest()->getParam('items');
        if ($items && $this->getRequest()->isAjax()) {
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
