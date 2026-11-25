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

use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Model\UrlInterface;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Plugin\BackendLogoUrl;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BackendLogoUrlTest
 * @package Mageplaza\Reports\Test\Unit\Plugin
 */
class BackendLogoUrlTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private Data|MockObject $helperDataMock;

    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface|MockObject $backendUrlMock;

    /**
     * @var BackendHelper|MockObject
     */
    private BackendHelper|MockObject $subjectMock;

    /**
     * @var BackendLogoUrl
     */
    private BackendLogoUrl $plugin;

    protected function setUp(): void
    {
        $this->helperDataMock = $this->createMock(Data::class);
        $this->backendUrlMock = $this->createMock(UrlInterface::class);
        $this->subjectMock    = $this->createMock(BackendHelper::class);

        $this->plugin = new BackendLogoUrl($this->helperDataMock, $this->backendUrlMock);
    }

    public function testAfterGetHomePageUrlReturnsDashboardRouteWhenEnabled(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(true);

        $this->backendUrlMock->expects($this->once())
            ->method('getRouteUrl')
            ->with('mpreports/dashboard')
            ->willReturn('http://x/mpreports/dashboard');

        $result = $this->plugin->afterGetHomePageUrl($this->subjectMock, 'original-url');

        $this->assertEquals('http://x/mpreports/dashboard', $result);
    }

    public function testAfterGetHomePageUrlReturnsOriginalResultWhenDisabled(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(false);

        $this->backendUrlMock->expects($this->never())
            ->method('getRouteUrl');

        $result = $this->plugin->afterGetHomePageUrl($this->subjectMock, 'original-url');

        $this->assertEquals('original-url', $result);
    }
}
