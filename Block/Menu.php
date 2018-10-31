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
use Magento\Customer\Model\Customer\Source\Group;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Menu
 * @package Mageplaza\Reports\Block
 */
class Menu extends Template
{
    /**
     * @var Group
     */
    protected $customerGroup;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var array
     */
    protected $girdName = [];

    /**
     * @var array
     */
    protected $menuUrls = [];

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param Group $customerGroup
     * @param Data $helperData
     * @param array $girdName
     * @param array $menuUrls
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Group $customerGroup,
        Data $helperData,
        array $girdName = [],
        array $menuUrls = [],
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->customerGroup = $customerGroup;
        $this->helperData    = $helperData;
        $this->girdName      = $girdName;
        $this->menuUrls      = $menuUrls;
    }

    /**
     * @return array
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup->toOptionArray();
    }

    /**
     * @return array
     */
    public function getDateRange()
    {
        $dateRange = $this->helperData->getDateRange();
        if ($startDate = $this->getRequest()->getParam('startDate')) {
            $dateRange[0] = $startDate;
        }
        if ($endDate = $this->getRequest()->getParam('endDate')) {
            $dateRange[1] = $endDate;
        }

        return $dateRange;
    }

    /**
     * @return string
     */
    public function getGridName()
    {
        $fullActionName = $this->getRequest()->getFullActionName();

        return isset($this->girdName[$fullActionName]) ? $this->girdName[$fullActionName] : '';
    }

    /**
     * @return array
     */
    public function getMenuUrls()
    {
        $urls = [];
        foreach ($this->menuUrls as $path => $label) {
            $urls[] = [
                'label' => __($label),
                'path'  => $this->geturl($path)
            ];
        }

        return $urls;
    }
}