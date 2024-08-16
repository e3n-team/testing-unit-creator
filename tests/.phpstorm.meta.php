<?php
// see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

    // edit to your needs
    override(
        \e3n\Dev\Test\Factory\FactoryProvider::get(0),
        map([
                '\e3n\Dev\Tests\Test\Mock\Entity\EntityA' => \e3n\Dev\Tests\Test\Mock\Factory\EntityAFactory::class,
                '\e3n\Dev\Tests\Test\Mock\Entity\EntityB' => \e3n\Dev\Tests\Test\Mock\Factory\EntityBFactory::class,
            ])
    );
}
