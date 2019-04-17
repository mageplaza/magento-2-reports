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

use Magento\Backend\Model\Menu\Builder\AbstractCommand;
use Mageplaza\Reports\Helper\Data;

/**
 * Class MoveMenu
 * @package Mageplaza\Reports\Plugin
 */
class MoveMenu
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
     * @param AbstractCommand $subject
     * @param $itemParams
     *
     * @return mixed
     * @SuppressWarnings(Unused)
     */
    public function afterExecute(AbstractCommand $subject, $itemParams)
    {
        if ($itemParams['id'] === 'Mageplaza_Reports::dashboard' && !$this->helper->isEnabledDashboard()) {
            $itemParams['removed'] = true;
        }

        return $itemParams;
    }
}
