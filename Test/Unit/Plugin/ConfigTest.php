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

declare(strict_types=1);

namespace Mageplaza\Reports\Test\Unit\Plugin;

use Magento\Framework\App\Route\Config as BackendConfig;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Plugin\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest
 * @package Mageplaza\Reports\Test\Unit\Plugin
 */
class ConfigTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private Data|MockObject $helperMock;

    /**
     * @var BackendConfig|MockObject
     */
    private BackendConfig|MockObject $subjectMock;

    /**
     * @var Config
     */
    private Config $plugin;

    protected function setUp(): void
    {
        $this->helperMock  = $this->createMock(Data::class);
        $this->subjectMock = $this->createMock(BackendConfig::class);

        $this->plugin = new Config($this->helperMock);
    }

    public function testAfterGetRouteByFrontNameReturnsResultWhenTruthy(): void
    {
        $this->helperMock->expects($this->never())
            ->method('versionCompare');

        $result = $this->plugin->afterGetRouteByFrontName($this->subjectMock, 'catalog');

        $this->assertEquals('catalog', $result);
    }

    public function testAfterGetRouteByFrontNameReturnsAdminhtmlWhenEmptyAndVersionMatches(): void
    {
        $this->helperMock->expects($this->once())
            ->method('versionCompare')
            ->with('2.2.8', '=')
            ->willReturn(true);

        $result = $this->plugin->afterGetRouteByFrontName($this->subjectMock, '');

        $this->assertEquals('adminhtml', $result);
    }

    public function testAfterGetRouteByFrontNameReturnsEmptyWhenEmptyAndVersionDoesNotMatch(): void
    {
        $this->helperMock->expects($this->once())
            ->method('versionCompare')
            ->with('2.2.8', '=')
            ->willReturn(false);

        $result = $this->plugin->afterGetRouteByFrontName($this->subjectMock, '');

        $this->assertEquals('', $result);
    }
}
