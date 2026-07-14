<?php
declare(strict_types=1);
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

namespace Mageplaza\Reports\Test\Unit\Model\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Group;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\Reports\Block\Dashboard\AverageOrder;
use Mageplaza\Reports\Block\Dashboard\TotalSales;
use Mageplaza\Reports\Model\Api\CardManagement;
use Mageplaza\Reports\Model\Api\Data\Card;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Class CardManagementTest
 * @package Mageplaza\Reports\Test\Unit\Model\Api
 */
class CardManagementTest extends TestCase
{
    private RequestInterface|MockObject $requestMock;

    private StoreManagerInterface|MockObject $storeManagerMock;

    private CardManagement $model;

    protected function setUp(): void
    {
        $this->requestMock      = $this->createMock(RequestInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $objectManager = new ObjectManager($this);
        $this->model   = $objectManager->getObject(CardManagement::class, [
            'request'      => $this->requestMock,
            'storeManager' => $this->storeManagerMock,
        ]);
    }

    /**
     * Stub RequestInterface::getParam() to resolve from a simple key => value map,
     * falling back to the caller-supplied default value otherwise.
     */
    private function stubGetParam(array $map): void
    {
        $this->requestMock->method('getParam')->willReturnCallback(
            static fn ($key, $default = null) => $map[$key] ?? $default
        );
    }

    /**
     * @return mixed
     */
    private function invokeAddFilter()
    {
        $method = new ReflectionMethod(CardManagement::class, 'addFilter');
        $method->setAccessible(true);

        return $method->invoke($this->model);
    }

    /**
     * @return mixed
     */
    private function invokeGetCard(string $cardName, array $filters)
    {
        $method = new ReflectionMethod(CardManagement::class, 'getCard');
        $method->setAccessible(true);

        return $method->invoke($this->model, $cardName, $filters);
    }

    public function testAddFilterWebsiteBranch(): void
    {
        $this->requestMock->method('getParams')->willReturn([]);
        $this->stubGetParam(['website' => 'base']);
        $this->requestMock->expects($this->once())->method('setParams');

        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStoreIds')->willReturn([1, 2]);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with('base')
            ->willReturn($websiteMock);
        $this->storeManagerMock->expects($this->never())->method('getGroup');

        $result = $this->invokeAddFilter();

        $this->assertSame([0, 2, [1]], $result);
    }

    public function testAddFilterGroupBranch(): void
    {
        $this->requestMock->method('getParams')->willReturn([]);
        $this->stubGetParam(['group' => 'g1']);
        $this->requestMock->expects($this->once())->method('setParams');

        $groupMock = $this->createMock(Group::class);
        $groupMock->method('getStoreIds')->willReturn([3, 4]);
        $this->storeManagerMock->expects($this->once())
            ->method('getGroup')
            ->with('g1')
            ->willReturn($groupMock);
        $this->storeManagerMock->expects($this->never())->method('getWebsite');

        $result = $this->invokeAddFilter();

        $this->assertSame([0, 4, [3]], $result);
    }

    public function testAddFilterStoreBranch(): void
    {
        $this->requestMock->method('getParams')->willReturn([]);
        $this->stubGetParam(['store' => '5']);
        $this->requestMock->expects($this->once())->method('setParams');

        $this->storeManagerMock->expects($this->never())->method('getWebsite');
        $this->storeManagerMock->expects($this->never())->method('getGroup');

        $result = $this->invokeAddFilter();

        $this->assertSame([1, 5, [5]], $result);
    }

    public function testAddFilterNoneBranch(): void
    {
        $this->requestMock->method('getParams')->willReturn([]);
        $this->stubGetParam([]);
        $this->requestMock->expects($this->once())->method('setParams');

        $this->storeManagerMock->expects($this->never())->method('getWebsite');
        $this->storeManagerMock->expects($this->never())->method('getGroup');

        $result = $this->invokeAddFilter();

        $this->assertSame([null, null, null], $result);
    }

    public function testAddFilterInjectsDateRange(): void
    {
        $this->requestMock->method('getParams')->willReturn([
            'startDate'        => '2024-01-01',
            'endDate'          => '2024-01-31',
            'compareStartDate' => '2023-12-01',
            'compareEndDate'   => '2023-12-31',
        ]);
        $this->stubGetParam([]);

        $this->requestMock->expects($this->once())
            ->method('setParams')
            ->with($this->callback(static function ($params) {
                return isset($params['dateRange'])
                    && $params['dateRange'][0] === '2024-01-01'
                    && $params['dateRange'][1] === '2024-01-31'
                    && $params['dateRange'][2] === '2023-12-01'
                    && $params['dateRange'][3] === '2023-12-31';
            }));

        $this->invokeAddFilter();
    }

    public function testGetCardReturnsNullForUnknownName(): void
    {
        $result = $this->invokeGetCard('nope', [null, null, null]);

        $this->assertNull($result);
    }

    public function testGetCardTotalSales(): void
    {
        $totalSalesMock = $this->createMock(TotalSales::class);
        $totalSalesMock->expects($this->once())->method('getTotal')->with(false)->willReturn(99);
        $totalSalesMock->expects($this->once())->method('getRate')->willReturn(12.5);
        $totalSalesMock->expects($this->once())->method('getChartData')->willReturn(['c']);

        $objectManager = new ObjectManager($this);
        $model         = $objectManager->getObject(CardManagement::class, [
            'request'    => $this->requestMock,
            'totalSales' => $totalSalesMock,
        ]);

        $method = new ReflectionMethod(CardManagement::class, 'getCard');
        $method->setAccessible(true);
        $result = $method->invoke($model, 'totalSales', [null, null, null]);

        $this->assertInstanceOf(Card::class, $result);
        $this->assertSame('totalSales', $result->getName());
        $this->assertSame(['total' => 99, 'rate' => 12.5, 'chartData' => ['c']], $result->getData());
    }

    public function testGetCardAverageOrder(): void
    {
        $averageOrderMock = $this->createMock(AverageOrder::class);
        $averageOrderMock->expects($this->once())->method('getTotal')->with(false)->willReturn(42);

        $objectManager = new ObjectManager($this);
        $model         = $objectManager->getObject(CardManagement::class, [
            'request'          => $this->requestMock,
            'averageOrderCard' => $averageOrderMock,
        ]);

        $method = new ReflectionMethod(CardManagement::class, 'getCard');
        $method->setAccessible(true);
        $result = $method->invoke($model, 'averageOrder', [null, null, null]);

        $this->assertInstanceOf(Card::class, $result);
        $this->assertSame('averageOrder', $result->getName());
        $this->assertSame(['total' => 42], $result->getData());
    }

    public function testGetHappyPathReturnsNullForUnknownCard(): void
    {
        $this->requestMock->method('getParams')->willReturn([]);
        $this->stubGetParam([]);
        $this->requestMock->method('setParams');

        $result = $this->model->get('nope');

        $this->assertNull($result);
    }
}
