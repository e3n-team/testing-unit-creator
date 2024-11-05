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

use e3n\Test\Tests\Stub\StubWithVariadicBuildinType;
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(StubWithVariadicBuildinType::class)]
#[CoversClass(UnitCreatorTrait::class)]
#[Group('unit')]
class UnitCreatorVariadicArrayParameterTest extends TestCase
{
    /** @use UnitCreatorTrait<StubWithVariadicBuildinType> */
    use UnitCreatorTrait;

    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    /** @return array<string, mixed> */
    protected function getUnitConstructorParameters(): array
    {
        return ['valueA' => 'value_a', 'valueB' => ['value_b_1', 'value_b_2']];
    }

    public function testGetUnit(): void
    {
        $actual = $this->getUnit();

        self::assertInstanceOf(StubWithVariadicBuildinType::class, $actual);
        self::assertEquals(['value_b_1', 'value_b_2'], $actual->getValueB());
    }
}
