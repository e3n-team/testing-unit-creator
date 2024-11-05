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

use e3n\Test\Tests\Stub\StubB;
use e3n\Test\Tests\Stub\StubWithMultipleSameDependency;
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(StubWithMultipleSameDependency::class)]
#[CoversClass(UnitCreatorTrait::class)]
#[Group('unit')]
class UnitCreatorMultipleMocksWithSameTypeTest extends TestCase
{
    /** @use UnitCreatorTrait<StubWithMultipleSameDependency> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    public function testMock(): void
    {
        $actual = $this->getUnit();

        self::assertInstanceOf(StubWithMultipleSameDependency::class, $actual);

        self::assertInstanceOf(StubB::class, $actual->getValueB1());
        self::assertInstanceOf(StubB::class, $actual->getValueB2());

        self::assertNotSame($actual->getValueB1(), $actual->getValueB2());
        self::assertNotSame($this->mock(StubB::class, 'valueB1'), $this->mock(StubB::class, 'valueB2'));

        self::assertSame($this->mock(StubB::class, 'valueB1'), $actual->getValueB1());
        self::assertSame($this->mock(StubB::class, 'valueB2'), $actual->getValueB2());
    }
}
