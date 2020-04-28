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

namespace Mageplaza\Reports\Block\Dashboard;

use Magento\Backend\Block\Template;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Dashboard
 * @package Mageplaza\Reports\Block
 */
class Card extends Template
{

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_Reports::dashboard/card.phtml';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
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
     * @return mixed
     */
    public function getCard()
    {
        return $this->getData('mp_card');
    }

    /**
     * @param $card
     *
     * @return Card
     */
    public function setCard($card)
    {
        return $this->setData('mp_card', $card);
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return 'adminhtml';
    }
}
