<?php
/*
 * This file is part of e3n/testing-unit-creator
 *
 * Copyright (c) e3n GmbH & Co. KG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace e3n\Test\Tests\Unit;

use e3n\Test\Tests\Stub\StubA;
use e3n\Test\Tests\Stub\StubB;
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \e3n\Test\Tests\Stub\StubA
 * @covers \e3n\Test\UnitCreatorTrait
 * @group unit
 */
class UnitCreatorUnitTest extends TestCase
{
    /** @use UnitCreatorTrait<StubA> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    /** @return array<string, mixed> */
    protected function getUnitConstructorParameters(): array
    {
        return ['valueA' => 'value_a', 'valueC' => 1337];
    }

    public function testGetUnit(): void
    {
        $actual = $this->getUnit();

        self::assertInstanceOf(StubA::class, $actual);
    }

    public function testMock(): void
    {
        $this->getUnit();
        $actual = $this->mock(StubB::class);

        self::assertInstanceOf(StubB::class, $actual);
        self::assertInstanceOf(MockObject::class, $actual);
    }

    public function testMockExpectation(): void
    {
        $this->mock(StubB::class)
             ->expects(self::once())
             ->method('getA')
             ->willReturn('C');

        $actual = $this->getUnit()->callMethodOnB();

        self::assertSame('C', $actual);
    }

    public function testGetAbstractUnit(): void
    {
        $this->expectExceptionMessage(
            sprintf(
                'Called getAbstractUnit() for a non abstract unit `\\%s`. Use getUnit() instead.',
                StubA::class
            )
        );
        $this->getAbstractUnit();
    }
}
