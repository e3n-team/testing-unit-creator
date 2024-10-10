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

use e3n\Test\Tests\Stub\AbstractStub;
use e3n\Test\Tests\Stub\StubB;
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \e3n\Test\Tests\Stub\AbstractStub
 * @covers \e3n\Test\UnitCreatorTrait
 * @group unit
 */
class UnitCreatorAbstractUnitTest extends TestCase
{
    /** @use UnitCreatorTrait<AbstractStub> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    public function testGetAbstractUnit(): void
    {
        $actual = $this->getAbstractUnit();

        self::assertInstanceOf(AbstractStub::class, $actual);
    }

    public function testCallAbstractMethod(): void
    {
        $unit          = $this->getAbstractUnit();
        $expectedValue = 'Test';

        $unit->expects(self::once())
             ->method('abstractMethod')
             ->with('TestArgument')
             ->willReturn($expectedValue);

        $result = $unit->callAbstractMethod('TestArgument');

        self::assertEquals($expectedValue, $result);
    }

    public function testMock(): void
    {
        $this->getAbstractUnit();
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

        $actual = $this->getAbstractUnit()->callMethodOnB();

        self::assertSame('C', $actual);
    }

    public function testGetUnit(): void
    {
        $this->expectExceptionMessage(
            sprintf(
                'Called getUnit() for an abstract unit `\\%s`. Use getAbstractUnit() instead.',
                AbstractStub::class
            )
        );
        $this->getUnit();
    }
}
