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

namespace Mageplaza\Reports\Api\Data;

/**
 * Interface CardInterface
 * @package Mageplaza\Repoprts\Api\Data
 */
interface CardInterface
{
    const NAME = 'name';
    const DATA = 'data';

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @param string $key
     * @param null $index
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @param $key
     * @param null $value
     * @return mixed
     */
    public function setData($key, $value = null);
}
