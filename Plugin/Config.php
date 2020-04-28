<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

use Magento\Framework\App\Route\Config as BackendConfig;
use Mageplaza\Reports\Helper\Data;

/**
 * Class Config
 * @package Mageplaza\Reports\Plugin
 */
class Config
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * MoveMenu constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param BackendConfig $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetRouteByFrontName(BackendConfig $subject, $result)
    {
        if (!$result && $this->helper->versionCompare('2.2.8', '=')) {
            return 'adminhtml';
        }

        return $result;
    }
}
