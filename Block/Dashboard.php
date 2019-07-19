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

namespace Mageplaza\Reports\Block;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Model\CardsManageFactory;

/**
 * Class Dashboard
 * @package Mageplaza\Reports\Block
 */
class Dashboard extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Reports::dashboard.phtml';

    /**
     * @var CardsManageFactory
     */
    protected $_cardsManageFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param CardsManageFactory $cardsManageFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CardsManageFactory $cardsManageFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_cardsManageFactory = $cardsManageFactory;
        $this->_helperData         = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return Template|void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        if ($this->isEnabled()) {
            foreach ($this->getMap() as $alias => $block) {
                $this->addChild($alias, $block);
            }
            $this->getLayout()->unsetElement('dashboard');
        } else {
            $this->getLayout()->unsetElement('ar_dashboard');
        }

        parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }

        return $this->getUrl('adminhtml/*/*', ['_current' => true, 'period' => null]);
    }

    /**
     * @return int
     */
    public function isCompare()
    {
        return $this->_helperData->isCompare() ? 1 : 0;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_helperData->isEnabled();
    }

    /**
     * @return array
     */
    public function getCards()
    {
        try {
            $result = $this->_cardsManageFactory->create();
        } catch (Exception $e) {
            $result = [];
            $this->_logger->critical($e);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getMap()
    {
        return $this->_cardsManageFactory->getMap();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDate()
    {
        return Data::jsonEncode($this->_helperData->getDateRange());
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return 'adminhtml';
    }

    /**
     * @return array
     * @return array
     */
    public function getGridStackConfig()
    {
        $config = [
            'url'         => $this->getUrl('mpreports/cards/saveposition', ['form_key' => $this->getFormKey()]),
            'loadCardUrl' => $this->getUrl('mpreports/cards/loadcard', ['form_key' => $this->getFormKey()])
        ];

        return $config;
    }
}
