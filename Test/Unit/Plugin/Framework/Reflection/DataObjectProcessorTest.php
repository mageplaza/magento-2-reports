<?php
declare(strict_types=1);

namespace Mageplaza\Reports\Test\Unit\Plugin\Framework\Reflection;

use Mageplaza\Reports\Api\Data\CardInterface;
use Mageplaza\Reports\Helper\Data;
use Mageplaza\Reports\Plugin\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Reflection\FieldNamer;
use Magento\Framework\Reflection\MethodsMap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DataObjectProcessorTest
 * @package Mageplaza\Reports\Test\Unit\Plugin\Framework\Reflection
 */
class DataObjectProcessorTest extends TestCase
{
    /**
     * @var MethodsMap|MockObject
     */
    private MethodsMap|MockObject $methodsMapProcessorMock;

    /**
     * @var FieldNamer|MockObject
     */
    private FieldNamer|MockObject $fieldNamerMock;

    /**
     * @var Data|MockObject
     */
    private Data|MockObject $helperDataMock;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor|MockObject
     */
    private \Magento\Framework\Reflection\DataObjectProcessor|MockObject $subjectMock;

    /**
     * @var DataObjectProcessor
     */
    private DataObjectProcessor $plugin;

    protected function setUp(): void
    {
        $this->methodsMapProcessorMock = $this->createMock(MethodsMap::class);
        $this->fieldNamerMock          = $this->createMock(FieldNamer::class);
        $this->helperDataMock          = $this->createMock(Data::class);
        $this->subjectMock             = $this->createMock(\Magento\Framework\Reflection\DataObjectProcessor::class);

        $this->plugin = new DataObjectProcessor(
            $this->methodsMapProcessorMock,
            $this->fieldNamerMock,
            $this->helperDataMock
        );
    }

    public function testAfterBuildOutputDataArrayReturnsUnchangedWhenDisabled(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->methodsMapProcessorMock->expects($this->never())
            ->method('getMethodsMap');

        $outputData = ['foo' => 'bar'];
        $dataObject = $this->createMock(CardInterface::class);

        $result = $this->plugin->afterBuildOutputDataArray(
            $this->subjectMock,
            $outputData,
            $dataObject,
            CardInterface::class
        );

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testAfterBuildOutputDataArrayReturnsUnchangedWhenTypeIsNotCardInterface(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->methodsMapProcessorMock->expects($this->once())
            ->method('getMethodsMap')
            ->with('Some\\Other\\Type')
            ->willReturn([]);

        $outputData = ['foo' => 'bar'];
        $dataObject = $this->createMock(CardInterface::class);

        $result = $this->plugin->afterBuildOutputDataArray(
            $this->subjectMock,
            $outputData,
            $dataObject,
            'Some\\Other\\Type'
        );

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testAfterBuildOutputDataArraySetsDataKeyWhenFieldNameIsData(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->methodsMapProcessorMock->expects($this->once())
            ->method('getMethodsMap')
            ->with(CardInterface::class)
            ->willReturn(['getData' => []]);

        $this->methodsMapProcessorMock->expects($this->once())
            ->method('isMethodValidForDataField')
            ->with(CardInterface::class, 'getData')
            ->willReturn(true);

        $this->fieldNamerMock->expects($this->once())
            ->method('getFieldNameForMethodName')
            ->with('getData')
            ->willReturn('data');

        $dataObject = $this->createMock(CardInterface::class);
        $dataObject->expects($this->once())
            ->method('getData')
            ->willReturn('X');

        $outputData = ['foo' => 'bar'];

        $result = $this->plugin->afterBuildOutputDataArray(
            $this->subjectMock,
            $outputData,
            $dataObject,
            CardInterface::class
        );

        $this->assertEquals('X', $result['data']);
    }

    public function testAfterBuildOutputDataArrayLeavesOutputUnchangedWhenFieldNameIsNotData(): void
    {
        $this->helperDataMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->methodsMapProcessorMock->expects($this->once())
            ->method('getMethodsMap')
            ->with(CardInterface::class)
            ->willReturn(['getName' => []]);

        $this->methodsMapProcessorMock->expects($this->once())
            ->method('isMethodValidForDataField')
            ->with(CardInterface::class, 'getName')
            ->willReturn(true);

        $this->fieldNamerMock->expects($this->once())
            ->method('getFieldNameForMethodName')
            ->with('getName')
            ->willReturn('name');

        $dataObject = $this->createMock(CardInterface::class);
        $dataObject->method('getName')->willReturn('Some Name');

        $outputData = ['foo' => 'bar'];

        $result = $this->plugin->afterBuildOutputDataArray(
            $this->subjectMock,
            $outputData,
            $dataObject,
            CardInterface::class
        );

        $this->assertEquals(['foo' => 'bar'], $result);
        $this->assertArrayNotHasKey('data', $result);
    }
}
