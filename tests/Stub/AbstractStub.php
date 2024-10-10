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

abstract class AbstractStub
{
    public function __construct(private readonly StubB $valueB)
    {
    }

    abstract protected function abstractMethod(string $value): string;

    public function callAbstractMethod(string $value): string
    {
        return $this->abstractMethod($value);
    }

    public function callMethodOnB(): string
    {
        return $this->valueB->getA();
    }
}
