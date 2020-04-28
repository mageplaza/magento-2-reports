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

namespace Mageplaza\Reports\Plugin\Model;

use Magento\Backend\Model\Url;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\Reports\Helper\Data;

/**
 * Class StartupPageUrl
 * @package Mageplaza\Reports\Plugin\Model
 */
class StartupPageUrl
{
    /**
     * @var Data
     */
    protected $resultRedirectFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * StartupPageUrl constructor.
     *
     * @param RedirectFactory $resultRedirectFactory
     * @param Data $helperData
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        Data $helperData
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_helperData           = $helperData;
    }

    /**
     * @param Url $url
     * @param     $result
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    public function afterGetStartupPageUrl(Url $url, $result)
    {
        if ($this->_helperData->isEnabledDashboard()) {
            $result = 'mpreports/dashboard';
        }

        return $result;
    }
}
