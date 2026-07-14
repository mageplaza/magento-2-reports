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

namespace Mageplaza\Reports\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\Reports\Model\CardsManageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CardsManageFactoryTest
 * @package Mageplaza\Reports\Test\Unit\Model
 */
class CardsManageFactoryTest extends TestCase
{
    private ManagerInterface|MockObject $eventManagerMock;

    private CardsManageFactory $model;

    protected function setUp(): void
    {
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);

        $objectManager = new ObjectManager($this);
        $this->model   = $objectManager->getObject(CardsManageFactory::class, [
            'eventManager' => $this->eventManagerMock,
            'map'          => ['a' => 'X', 'b' => 'Y'],
        ]);
    }

    public function testGetMapDispatchesEventAndReturnsMapData(): void
    {
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with(
                'mageplaza_report_init_cards',
                $this->callback(static function ($params) {
                    return isset($params['cards']) && $params['cards'] instanceof DataObject;
                })
            );

        $result = $this->model->getMap();

        $this->assertSame(['a' => 'X', 'b' => 'Y'], $result);
    }
}
