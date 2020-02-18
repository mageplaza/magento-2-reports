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
 * @package     Mageplaza_Repoprts
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Reports\Api;

use Mageplaza\Reports\Api\Data\CardInterface;

/**
 * Interface CardManagementInterface
 * @package Mageplaza\Reports\Api
 */
interface CardManagementInterface
{
    /**
     * Adds a gift card code to a specified cart.
     *
     * @param string $cardName The card ID.
     * @param array $filters The gift card code data.
     *
     * @return CardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function get($cardName);
}
