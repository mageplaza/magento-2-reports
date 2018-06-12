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

use Magento\Backend\Block\Template;
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
    protected $_template = 'dashboard/index.phtml';

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
    )
    {
        parent::__construct($context, $data);

        $this->_cardsManageFactory = $cardsManageFactory;
        $this->_helperData         = $helperData;
    }

    /**
     * @return Template|void
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * @throws \Exception
     */
    public function getCards()
    {
        return $this->_cardsManageFactory->create();
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
     */
    public function getDate()
    {
        return json_encode($this->_helperData->getDateRange());
    }
}
