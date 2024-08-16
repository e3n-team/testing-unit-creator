<?php
/*
 * This file is part of symfony-dev
 *
 * Copyright (c) e3n GmbH & Co. KG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Test;

use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Covers;
use phpDocumentor\Reflection\DocBlockFactory;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionNamedType;

/**
 * @template UNIT of object
 */
trait UnitTrait
{
    /** @var UNIT|null */
    private ?object $unit = null;

    /** @var (UNIT&MockObject)|null */
    private ?MockObject $abstractUnit = null;

    /** @var array<string, MockObject> */
    private array $mocks = [];

    /** @return UNIT */
    protected function getUnit(): object
    {
        if ($this->unit === null) {
            $unitClass      = $this->getUnitClass();
            $unitReflection = new ReflectionClass($unitClass);

            if ($unitReflection->isAbstract() === true) {
                throw new Exception('Please utilize the method getAbstractUnit() to instantiate an abstract unit');
            }

            $this->unit = $this->createUnit($unitReflection, $unitClass);
        }

        return $this->unit;
    }

    /**
     * Due a bug in PHPStorm's code completion we have to use union type for the return annotation.
     * But for real the return type is an intersection of UNIT and MockObject.
     *
     * @return UNIT|MockObject
     * @phpstan-return UNIT&MockObject
     * @psalm-return  UNIT&MockObject
     */
    protected function getAbstractUnit(): object
    {
        if ($this->abstractUnit === null) {
            $unitClass = $this->getUnitClass();

            $unitReflection = new ReflectionClass($unitClass);

            if ($unitReflection->isAbstract() === false) {
                throw new Exception('please utilize getUnit() to instantiate a non abstract unit');
            }

            $this->abstractUnit = $this->createAbstractUnit($unitReflection, $unitClass);
        }

        return $this->abstractUnit;
    }

    /** @return array<string, mixed> */
    protected function getUnitConstructorParameters(): array
    {
        return [];
    }

    /**
     * @template MOCK of object
     * @param class-string<MOCK> $class
     * @return MOCK&MockObject
     */
    protected function mock(string $class): object
    {
        if (isset($this->mocks[$class]) === false) {
            $this->mocks[$class] = $this->createMock($class);
        }

        /** @var MOCK&MockObject $mock */
        $mock = $this->mocks[$class];

        return $mock;
    }

    protected function clearUnit(): void
    {
        $this->unit         = null;
        $this->abstractUnit = null;
        $this->mocks        = [];
    }

    /**
     * @param ReflectionClass<UNIT> $unitReflection
     * @param class-string<UNIT> $unitClass
     * @return UNIT
     */
    private function createUnit(ReflectionClass $unitReflection, string $unitClass): object
    {
        $parameters = $this->buildParameters($unitReflection);

        return new $unitClass(...$parameters);
    }

    /**
     * @param ReflectionClass<UNIT> $unitReflection
     * @param class-string<UNIT> $unitClass
     * @return UNIT&MockObject
     */
    private function createAbstractUnit(ReflectionClass $unitReflection, string $unitClass): object
    {
        $parameters = $this->buildParameters($unitReflection);

        $abstractMethods = [];
        foreach ($unitReflection->getMethods() as $method) {
            if ($method->isAbstract() === true) {
                $abstractMethods[] = $method->getName();
            }
        }

        return $this->getMockBuilder($unitClass)
                    ->onlyMethods($abstractMethods)
                    ->setConstructorArgs($parameters)
                    ->getMock();
    }

    /** @return class-string<UNIT> */
    private function getUnitClass(): string
    {
        $testCase        = new ReflectionClass($this);
        $docBlockFactory = DocBlockFactory::createInstance();
        $docBlock        = $docBlockFactory->create($testCase);
        $coversTag       = $docBlock->getTagsByName('covers')[0];

        if ($coversTag instanceof Covers === false) {
            throw new Exception("Invalid @covers annotation given");
        }

        /** @var class-string<UNIT> $fqcn */
        $fqcn = (string)$coversTag->getReference();

        return $fqcn;
    }

    /**
     * @param ReflectionClass<UNIT> $unitReflection
     * @return array<string, mixed>
     */
    private function buildParameters(ReflectionClass $unitReflection): array
    {
        $parameters           = $this->getUnitConstructorParameters();
        $parameterReflections = $unitReflection->getConstructor()?->getParameters() ?? [];

        foreach ($parameterReflections as $parameterReflection) {
            $parameterName           = $parameterReflection->getName();
            $parameterReflectionType = $parameterReflection->getType();

            if ($parameterReflectionType instanceof ReflectionNamedType === false) {
                continue;
            }

            /** @var class-string<object> $parameterType */
            $parameterType = $parameterReflectionType->getName();

            if (array_key_exists($parameterName, $parameters)) {
                continue;
            }

            if ($parameterReflectionType->isBuiltin() === true) {
                continue;
            }

            $parameters[$parameterName]  = $this->mock($parameterType);
            $this->mocks[$parameterType] = $parameters[$parameterName];
        }

        return $parameters;
    }
}
