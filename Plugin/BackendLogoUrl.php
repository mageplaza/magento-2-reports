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

namespace Mageplaza\Reports\Plugin;

use Mageplaza\Reports\Helper\Data;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Model\UrlInterface;

/**
 * Class BackendLogoUrl
 * @package Mageplaza\Reports\Plugin
 */
class BackendLogoUrl
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var UrlInterface
     */
    protected $_backendUrl;


    /**
     * BackendLogoUrl constructor.
     * @param Data $helperData
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        Data $helperData,
        UrlInterface $backendUrl
    )
    {
        $this->_helperData = $helperData;
        $this->_backendUrl = $backendUrl;
    }

    /**
     * @param BackendHelper $data
     * @param $result
     * @return string
     */
    public function afterGetHomePageUrl(BackendHelper $data, $result)
    {
        if ($this->_helperData->isEnabled()) {
            $result = $this->_backendUrl->getRouteUrl('mpreports/dashboard');
        }
        return $result;
    }
}