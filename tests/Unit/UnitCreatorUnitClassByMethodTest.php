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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
class UnitCreatorUnitClassByMethodTest extends TestCase
{
    /** @use UnitCreatorTrait<StubA> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    /** @return class-string<StubA> */
    protected function getUnitClass(): string
    {
        return StubA::class;
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
}
