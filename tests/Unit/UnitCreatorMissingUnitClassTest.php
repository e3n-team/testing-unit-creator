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
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UnitCreatorMissingUnitClassTest extends TestCase
{
    /** @use UnitCreatorTrait<StubA> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    public function testGetUnit(): void
    {
        $this->expectExceptionMessage(
            'Provide a unit class by @covers annotation, #[CoversClass] attribute or getUnitClass() method.'
        );

        $actual = $this->getUnit();

        self::assertInstanceOf(StubA::class, $actual);
    }
}
