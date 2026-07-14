<?php
declare(strict_types=1);

namespace Mageplaza\Reports\Test\Unit\Model\Api\Data;

use Mageplaza\Reports\Model\Api\Data\Card;
use PHPUnit\Framework\TestCase;

/**
 * Class CardTest
 * @package Mageplaza\Reports\Test\Unit\Model\Api\Data
 */
class CardTest extends TestCase
{
    private Card $card;

    protected function setUp(): void
    {
        $this->card = new Card();
    }

    public function testSetGetNameRoundtrip(): void
    {
        $this->card->setName('myCard');

        $this->assertSame('myCard', $this->card->getName());
    }

    public function testNameBypassesDataBag(): void
    {
        $this->card->setName('foo');

        $this->assertNull($this->card->getData('name'));

        $this->card->setData(['name' => 'bar']);

        $this->assertSame('bar', $this->card->getData('name'));

        $this->assertSame('foo', $this->card->getName());
    }
}
