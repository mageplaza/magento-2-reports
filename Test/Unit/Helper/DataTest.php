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

namespace Mageplaza\Reports\Test\Unit\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Group;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\Reports\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionProperty;

class DataTest extends TestCase
{
    protected function setUp(): void
    {
        // No shared fixtures: each test builds the helper it needs via makeHelper()/makeRealHelper().
    }

    /**
     * @param string[] $onlyMethods
     */
    private function makeHelper(array $onlyMethods): Data|MockObject
    {
        return $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->onlyMethods($onlyMethods)
            ->getMock();
    }

    /**
     * @param array<string, mixed> $args
     */
    private function makeRealHelper(array $args = []): Data
    {
        return (new ObjectManager($this))->getObject(Data::class, $args);
    }

    /**
     * Inject a protected/private property (inherited or own) via reflection.
     */
    private function setProp(object $obj, string $prop, mixed $val): void
    {
        $ref = new ReflectionProperty(Data::class, $prop);
        $ref->setAccessible(true);
        $ref->setValue($obj, $val);
    }

    public static function formatDateProvider(): array
    {
        return [
            'Y-m-d format' => ['Y-m-d', '2024-01-15 10:30:00', '2024-01-15'],
            'Year only' => ['Y', '2020-06-01', '2020'],
        ];
    }

    #[DataProvider('formatDateProvider')]
    public function testFormatDate(string $format, string $date, string $expected): void
    {
        $helper = $this->makeRealHelper();

        $this->assertSame($expected, $helper->formatDate($format, $date));
    }

    public static function daysByDateRangeProvider(): array
    {
        return [
            'empty start' => ['', '2024-01-08', 0],
            'empty end' => ['2024-01-01', '', 0],
            'forward range' => ['2024-01-01', '2024-01-08', 7],
            'reversed range' => ['2024-01-08', '2024-01-01', -7],
        ];
    }

    #[DataProvider('daysByDateRangeProvider')]
    public function testGetDaysByDateRange(string $start, string $end, int $expected): void
    {
        $helper = $this->makeRealHelper();

        $result = $helper->getDaysByDateRange($start, $end);

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
    }

    public function testGetPeriodsDateReturnsInclusiveDateListByDefault(): void
    {
        $helper = $this->makeRealHelper();

        $result = $helper->getPeriodsDate('2024-01-01', '2024-01-03', 0, 0);

        $this->assertSame(['2024-01-01', '2024-01-02', '2024-01-03'], $result);
    }

    public function testGetPeriodsDateReturnsDataObjectsWhenIsObjectFlagSet(): void
    {
        $helper = $this->makeRealHelper();

        $result = $helper->getPeriodsDate('2024-01-01', '2024-01-03', 0, 1);

        $this->assertSame(['2024-01-01', '2024-01-02', '2024-01-03'], array_keys($result));
        foreach ($result as $value) {
            $this->assertInstanceOf(DataObject::class, $value);
        }
    }

    public function testGetPeriodsDateUsesDaysWhenEndDateIsNull(): void
    {
        $helper = $this->makeRealHelper();

        $result = $helper->getPeriodsDate('2024-01-01', null, 2, 0);

        $this->assertIsArray($result);
        $this->assertSame('2024-01-01', $result[0]);
        foreach ($result as $date) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
        }
    }

    public function testGetDateTimeRangeFormatDefaultFormatSameDay(): void
    {
        $helper = $this->makeHelper(['getTimezone']);
        $helper->method('getTimezone')->willReturn('UTC');

        $result = $helper->getDateTimeRangeFormat('2024-01-01', '2024-01-01', null, 'Y-m-d H:i:s');

        $this->assertSame(['2024-01-01 00:00:00', '2024-01-01 23:59:59'], $result);
    }

    public function testGetDateTimeRangeFormatCustomFormatDefaultsEndDateToStart(): void
    {
        $helper = $this->makeHelper(['getTimezone']);
        $helper->method('getTimezone')->willReturn('UTC');

        $result = $helper->getDateTimeRangeFormat('2024-01-01', null, null, 'Y-m-d');

        $this->assertSame(['2024-01-01', '2024-01-01'], $result);
    }

    public function testGetDateTimeRangeFormatConvertsToLocalTime(): void
    {
        $helper = $this->makeHelper(['getTimezone']);
        $helper->method('getTimezone')->willReturn('UTC');

        $result = $helper->getDateTimeRangeFormat('2024-01-01', null, 1, 'Y-m-d H:i:s');

        $this->assertCount(2, $result);
        foreach ($result as $value) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
        }
    }

    public function testIsEnabledDashboardTrueWhenBothEnabled(): void
    {
        $helper = $this->makeHelper(['isEnabled', 'getConfigGeneral']);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);

        $helper->method('isEnabled')->with(1)->willReturn(true);
        $helper->method('getConfigGeneral')->with('dashboard_enabled', 1)->willReturn(true);

        $this->assertTrue($helper->isEnabledDashboard(1));
    }

    public function testIsEnabledDashboardFalseWhenDashboardFlagDisabled(): void
    {
        $helper = $this->makeHelper(['isEnabled', 'getConfigGeneral']);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);

        $helper->method('isEnabled')->with(1)->willReturn(true);
        $helper->method('getConfigGeneral')->with('dashboard_enabled', 1)->willReturn(false);

        $this->assertFalse($helper->isEnabledDashboard(1));
    }

    public function testIsEnabledDashboardShortCircuitsWhenModuleDisabled(): void
    {
        $helper = $this->makeHelper(['isEnabled', 'getConfigGeneral']);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);

        $helper->method('isEnabled')->with(1)->willReturn(false);
        $helper->expects($this->never())->method('getConfigGeneral');

        $this->assertFalse($helper->isEnabledDashboard(1));
    }

    public function testAddStatusFilterAppliesStateAndStatusFilters(): void
    {
        $helper = $this->makeRealHelper();

        $collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter'])
            ->getMock();

        $calls = [];
        $collection->method('addFieldToFilter')->willReturnCallback(
            function ($field, $condition) use (&$calls, $collection) {
                $calls[] = [$field, $condition];

                return $collection;
            }
        );

        $result = $helper->addStatusFilter($collection);

        $this->assertSame($collection, $result);
        $this->assertCount(2, $calls);
        $this->assertSame(['state', ['nin' => ['pending_payment', 'new']]], $calls[0]);
        $this->assertSame(['status', ['neq' => 'Canceled']], $calls[1]);
    }

    private function makeCollectionMockCapturingCalls(array &$calls): AbstractCollection|MockObject
    {
        $collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter'])
            ->getMock();

        $collection->method('addFieldToFilter')->willReturnCallback(
            function ($field, $condition) use (&$calls, $collection) {
                $calls[] = [$field, $condition];

                return $collection;
            }
        );

        return $collection;
    }

    public function testAddStoreFilterFiltersByStoreWhenStoreParamPresent(): void
    {
        $helper = $this->makeRealHelper();

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturnMap([
            ['store', null, '7'],
        ]);
        $this->setProp($helper, '_request', $requestMock);
        $this->setProp($helper, 'storeManager', $this->createMock(StoreManagerInterface::class));

        $calls = [];
        $collection = $this->makeCollectionMockCapturingCalls($calls);

        $helper->addStoreFilter($collection);

        $this->assertCount(1, $calls);
        $this->assertSame(['store_id', '7'], $calls[0]);
    }

    public function testAddStoreFilterFiltersByWebsiteWhenWebsiteParamPresent(): void
    {
        $helper = $this->makeRealHelper();

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturnMap([
            ['store', null, null],
            ['website', null, 'w'],
        ]);
        $this->setProp($helper, '_request', $requestMock);

        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStoreIds')->willReturn([1, 2]);
        $storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $storeManagerMock->method('getWebsite')->with('w')->willReturn($websiteMock);
        $this->setProp($helper, 'storeManager', $storeManagerMock);

        $calls = [];
        $collection = $this->makeCollectionMockCapturingCalls($calls);

        $helper->addStoreFilter($collection);

        $this->assertCount(1, $calls);
        $this->assertSame(['store_id', ['in' => [1, 2]]], $calls[0]);
    }

    public function testAddStoreFilterFiltersByGroupWhenGroupParamPresent(): void
    {
        $helper = $this->makeRealHelper();

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturnMap([
            ['store', null, null],
            ['website', null, null],
            ['group', null, 'g'],
        ]);
        $this->setProp($helper, '_request', $requestMock);

        $groupMock = $this->createMock(Group::class);
        $groupMock->method('getStoreIds')->willReturn([3, 4]);
        $storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $storeManagerMock->method('getGroup')->with('g')->willReturn($groupMock);
        $this->setProp($helper, 'storeManager', $storeManagerMock);

        $calls = [];
        $collection = $this->makeCollectionMockCapturingCalls($calls);

        $helper->addStoreFilter($collection);

        $this->assertCount(1, $calls);
        $this->assertSame(['store_id', ['in' => [3, 4]]], $calls[0]);
    }

    public function testAddStoreFilterDoesNothingWhenNoParamsPresent(): void
    {
        $helper = $this->makeRealHelper();

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);
        $this->setProp($helper, 'storeManager', $this->createMock(StoreManagerInterface::class));

        $collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addFieldToFilter'])
            ->getMock();
        $collection->expects($this->never())->method('addFieldToFilter');

        $result = $helper->addStoreFilter($collection);

        $this->assertSame($collection, $result);
    }

    public function testAddTimeFilterUsesPeriodFieldForTaxCard(): void
    {
        $helper = $this->makeHelper(['getTimezone']);
        $helper->method('getTimezone')->willReturn('UTC');

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('card_id')->willReturn('tax');
        $this->setProp($helper, '_request', $requestMock);

        $calls = [];
        $collection = $this->makeCollectionMockCapturingCalls($calls);

        $helper->addTimeFilter($collection, '2024-01-01', null);

        $this->assertCount(2, $calls);
        $this->assertSame(['period', ['gteq' => '2024-01-01 00:00:00']], $calls[0]);
        $this->assertSame(['period', ['lteq' => '2024-01-01 23:59:59']], $calls[1]);
    }

    public function testAddTimeFilterUsesCreatedAtFieldForNonTaxCard(): void
    {
        $helper = $this->makeHelper(['getTimezone']);
        $helper->method('getTimezone')->willReturn('UTC');

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('card_id')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);

        $calls = [];
        $collection = $this->makeCollectionMockCapturingCalls($calls);

        $helper->addTimeFilter($collection, '2024-01-01', null);

        $this->assertCount(2, $calls);
        $this->assertSame(['created_at', ['gteq' => '2024-01-01 00:00:00']], $calls[0]);
        $this->assertSame(['created_at', ['lteq' => '2024-01-01 23:59:59']], $calls[1]);
    }

    public function testGetDateRangeReturnsCompareDatesWhenCompareEnabled(): void
    {
        $helper = $this->makeHelper(['isCompare', 'getTimezone']);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('dateRange')->willReturn(
            ['2024-01-01', '2024-01-31', '2023-12-01', '2023-12-31']
        );
        $this->setProp($helper, '_request', $requestMock);
        $this->setProp($helper, '_logger', $this->createMock(LoggerInterface::class));

        $helper->method('isCompare')->willReturn(true);

        $result = $helper->getDateRange('Y-m-d');

        $this->assertSame(['2024-01-01', '2024-01-31', '2023-12-01', '2023-12-31'], $result);
    }

    public function testGetDateRangeReturnsNullCompareDatesWhenCompareDisabled(): void
    {
        $helper = $this->makeHelper(['isCompare', 'getTimezone']);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('dateRange')->willReturn(
            ['2024-01-01', '2024-01-31', '2023-12-01', '2023-12-31']
        );
        $this->setProp($helper, '_request', $requestMock);
        $this->setProp($helper, '_logger', $this->createMock(LoggerInterface::class));

        $helper->method('isCompare')->willReturn(false);

        $result = $helper->getDateRange('Y-m-d');

        $this->assertSame(['2024-01-01', '2024-01-31', null, null], $result);
    }

    public function testGetDateRangeFallsBackToDefaultRangeWhenNoDateRangeParam(): void
    {
        $helper = $this->makeHelper(['isCompare', 'getTimezone']);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('dateRange')->willReturn(null);
        $this->setProp($helper, '_request', $requestMock);
        $this->setProp($helper, '_logger', $this->createMock(LoggerInterface::class));

        $helper->method('getTimezone')->willReturn('UTC');

        $result = $helper->getDateRange('Y-m-d');

        $this->assertCount(4, $result);
        foreach ($result as $value) {
            $this->assertIsString($value);
            $this->assertNotNull($value);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $value);
        }
    }

    public function testGetDateRangeReturnsNullsAndLogsOnInvalidDate(): void
    {
        $helper = $this->makeHelper(['isCompare', 'getTimezone']);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')->with('dateRange')->willReturn(['not-a-date', 'x']);
        $this->setProp($helper, '_request', $requestMock);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())->method('critical');
        $this->setProp($helper, '_logger', $loggerMock);

        $helper->method('isCompare')->willReturn(false);

        $result = $helper->getDateRange('Y-m-d');

        $this->assertSame([null, null, null, null], $result);
    }
}
