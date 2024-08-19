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

class StubA
{
    public function __construct(
        private readonly string $valueA,
        private readonly StubB  $valueB,
        private readonly int    $valueC
    ) {
    }

    public function getValueA(): string
    {
        return $this->valueA;
    }

    public function callMethodOnB(): string
    {
        return $this->valueB->getA();
    }

    public function getValueC(): int
    {
        return $this->valueC;
    }
}
