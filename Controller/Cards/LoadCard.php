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
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Reports\Block\Dashboard\Card;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Model\CardsManageFactory;

/**
 * Class SavePosition
 * @package Mageplaza\Reports\Controller\Cards
 */
class LoadCard extends Action
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
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $id     = $this->getRequest()->getParam('card_id');
        $layout = $this->_view->getLayout();
        $data   = [];

        $cardsManager = $this->_cardsManageFactory->create();
        if ($id && isset($cardsManager[$id]) && $this->getRequest()->isAjax()) {
            try {
                /** @var Card $block */
                $block = $layout->createBlock(Card::class);
                $block->setCard($cardsManager[$id]);
                $data['html'] = $block->toHtml();
            } catch (Exception $exception) {
                $data['message'] = $exception->getMessage();
            }
        }
        if (empty($data)) {
            $data['message'] = __('Can not be load Card');
        }

        return $this->getResponse()->representJson(Data::jsonEncode($data));
    }
}
