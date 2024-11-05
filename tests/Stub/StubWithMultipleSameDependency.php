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

class StubWithMultipleSameDependency
{
    public function __construct(
        private readonly StubB $valueB1,
        private readonly StubB $valueB2,
    ) {
    }

    public function getValueB1(): StubB
    {
        return $this->valueB1;
    }

    public function getValueB2(): StubB
    {
        return $this->valueB2;
    }
}
