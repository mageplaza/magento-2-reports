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

namespace Mageplaza\Reports\Block\Store;

use Magento\Backend\Block\Store\Switcher as BackendSwitcher;

/**
 * Class Switcher
 * @package Mageplaza\Reports\Block\Store
 */
class Switcher extends BackendSwitcher
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Mageplaza_Reports::store/switcher.phtml';
}
