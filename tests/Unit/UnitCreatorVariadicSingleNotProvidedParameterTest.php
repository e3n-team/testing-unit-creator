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
use e3n\Test\Tests\Stub\StubWithVariadicObjectType;
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(StubWithVariadicObjectType::class)]
#[CoversClass(UnitCreatorTrait::class)]
#[Group('unit')]
class UnitCreatorVariadicSingleNotProvidedParameterTest extends TestCase
{
    /** @use UnitCreatorTrait<StubWithVariadicObjectType> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    /** @return array<string, mixed> */
    protected function getUnitConstructorParameters(): array
    {
        return ['valueA' => 'value_a'];
    }

    public function testGetUnit(): void
    {
        $actual = $this->getUnit();

        self::assertInstanceOf(StubWithVariadicObjectType::class, $actual);
        self::assertInstanceOf(StubB::class, $actual->getValueB()[0]);
    }
}
