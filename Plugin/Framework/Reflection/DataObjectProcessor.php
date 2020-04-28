<?php

namespace Mageplaza\Reports\Plugin\Framework\Reflection;

use Magento\Framework\Reflection\FieldNamer;
use Magento\Framework\Reflection\MethodsMap;
use Mageplaza\Reports\Api\Data\CardInterface;
use Mageplaza\Reports\Helper\Data;
use ReflectionException;

/**
 * Class DataObjectProcessor
 * @package Mageplaza\Reports\Plugin\Framework\Reflection
 */
class DataObjectProcessor
{
    /**
     * @var MethodsMap
     */
    private $methodsMapProcessor;

    /**
     * @var FieldNamer
     */
    private $fieldNamer;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * DataObjectProcessor constructor.
     *
     * @param MethodsMap $methodsMapProcessor
     * @param FieldNamer $fieldNamer
     * @param Data $helperData
     */
    public function __construct(
        MethodsMap $methodsMapProcessor,
        FieldNamer $fieldNamer,
        Data $helperData
    ) {
        $this->methodsMapProcessor = $methodsMapProcessor;
        $this->fieldNamer          = $fieldNamer;
        $this->helperData          = $helperData;
    }

    /**
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param $dataObject
     * @param $dataObjectType
     * @param $outputData
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function afterBuildOutputDataArray(
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        $outputData,
        $dataObject,
        $dataObjectType
    ) {
        if (!$this->helperData->isEnabled()) {
            return $outputData;
        }
        $methods = $this->methodsMapProcessor->getMethodsMap($dataObjectType);
        if (strpos($dataObjectType, CardInterface::class) === false) {
            return $outputData;
        }
        foreach (array_keys($methods) as $methodName) {
            if (!$this->methodsMapProcessor->isMethodValidForDataField($dataObjectType, $methodName)) {
                continue;
            }
            $key   = $this->fieldNamer->getFieldNameForMethodName($methodName);
            $value = $dataObject->{$methodName}();
            if ($key === 'data') {
                $outputData[$key] = $value;
            }
        }

        return $outputData;
    }
}
