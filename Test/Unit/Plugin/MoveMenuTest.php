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

use Magento\Backend\Model\Menu\Builder\AbstractCommand;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Plugin\MoveMenu;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MoveMenuTest
 * @package Mageplaza\Reports\Test\Unit\Plugin
 */
class MoveMenuTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private Data|MockObject $helperMock;

    /**
     * @var AbstractCommand|MockObject
     */
    private AbstractCommand|MockObject $subjectMock;

    /**
     * @var MoveMenu
     */
    private MoveMenu $plugin;

    protected function setUp(): void
    {
        $this->helperMock  = $this->createMock(Data::class);
        $this->subjectMock = $this->createMock(AbstractCommand::class);

        $this->plugin = new MoveMenu($this->helperMock);
    }

    public function testAfterExecuteMarksRemovedWhenMatchingIdAndDisabled(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(false);

        $itemParams = ['id' => 'Mageplaza_Reports::dashboard'];

        $result = $this->plugin->afterExecute($this->subjectMock, $itemParams);

        $this->assertArrayHasKey('removed', $result);
        $this->assertTrue($result['removed']);
    }

    public function testAfterExecuteDoesNotMarkRemovedWhenMatchingIdAndEnabled(): void
    {
        $this->helperMock->expects($this->once())
            ->method('isEnabledDashboard')
            ->willReturn(true);

        $itemParams = ['id' => 'Mageplaza_Reports::dashboard'];

        $result = $this->plugin->afterExecute($this->subjectMock, $itemParams);

        $this->assertArrayNotHasKey('removed', $result);
    }

    public function testAfterExecuteLeavesItemUnchangedWhenIdDoesNotMatch(): void
    {
        $this->helperMock->expects($this->never())
            ->method('isEnabledDashboard');

        $itemParams = ['id' => 'Some_Other::thing'];

        $result = $this->plugin->afterExecute($this->subjectMock, $itemParams);

        $this->assertEquals(['id' => 'Some_Other::thing'], $result);
    }
}
