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

namespace e3n\Test\Tests\Stub;

class StubWithVariadicObjectType
{
    /** @var array<int|string, StubB> */
    private readonly array $valueB;

    public function __construct(
        private readonly string $valueA,
        StubB                   ...$valueB
    ) {
        $this->valueB = $valueB;
    }

    public function getValueA(): string
    {
        return $this->valueA;
    }

    /** @return array<int|string, StubB> */
    public function getValueB(): array
    {
        return $this->valueB;
    }
}
