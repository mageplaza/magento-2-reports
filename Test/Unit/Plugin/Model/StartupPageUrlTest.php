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

namespace Mageplaza\Reports\Test\Unit\Plugin\Model;

use Magento\Backend\Model\Url;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Plugin\Model\StartupPageUrl;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class StartupPageUrlTest
 * @package Mageplaza\Reports\Test\Unit\Plugin\Model
 */
class StartupPageUrlTest extends TestCase
{
    /**
     * @var RedirectFactory|MockObject
     */
    private RedirectFactory|MockObject $resultRedirectFactoryMock;

    /**
     * @var Data|MockObject
     */
    private Data|MockObject $helperDataMock;

    /**
     * @var Url|MockObject
     */
    private Url|MockObject $subjectMock;

    /**
     * @var StartupPageUrl
     */
    private StartupPageUrl $plugin;

    protected function setUp(): void
    {
        $this->resultRedirectFactoryMock = $this->createMock(RedirectFactory::class);
        $this->helperDataMock            = $this->createMock(Data::class);
        $this->subjectMock               = $this->createMock(Url::class);

        $this->plugin = new StartupPageUrl($this->resultRedirectFactoryMock, $this->helperDataMock);
    }

    public function testAfterGetStartupPageUrlReturnsDashboardRouteWhenEnabled(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(true);

        $result = $this->plugin->afterGetStartupPageUrl($this->subjectMock, 'admin/dashboard');

        $this->assertEquals('mpreports/dashboard', $result);
    }

    public function testAfterGetStartupPageUrlReturnsOriginalResultWhenDisabled(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(false);

        $result = $this->plugin->afterGetStartupPageUrl($this->subjectMock, 'admin/dashboard');

        $this->assertEquals('admin/dashboard', $result);
    }
}
