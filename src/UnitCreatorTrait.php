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

namespace e3n\Test;

use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Covers;
use phpDocumentor\Reflection\DocBlockFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @template UNIT of object
 */
trait UnitCreatorTrait
{
    /** @var UNIT|null */
    private ?object $unit = null;

    /** @var (UNIT&MockObject)|null */
    private ?MockObject $abstractUnit = null;

    /** @var array<string, array<null|int|string, MockObject>> */
    private array $mocks = [];

    /** @return UNIT */
    protected function getUnit(): object
    {
        if ($this->unit === null) {
            $unitClass      = $this->getUnitClass();
            $unitReflection = new ReflectionClass($unitClass);

            if ($unitReflection->isAbstract() === true) {
                $message = sprintf(
                    'Called %s() for an abstract unit `%s`. Use %s() instead.',
                    __FUNCTION__,
                    $unitClass,
                    'getAbstractUnit',
                );
                throw new Exception($message);
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
                $message = sprintf(
                    'Called %s() for a non abstract unit `%s`. Use %s() instead.',
                    __FUNCTION__,
                    $unitClass,
                    'getUnit',
                );
                throw new Exception($message);
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
    protected function mock(string $class, null|int|string $id = null): object
    {
        if (isset($this->mocks[$class][$id]) === false) {
            $this->mocks[$class][$id] = $this->createMock($class);
        }

        /** @var MOCK&MockObject $mock */
        $mock = $this->mocks[$class][$id];

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
    protected function getUnitClass(): string
    {
        $fqcn = $this->getUnitClassByDocBlock() ?? $this->getUnitClassByAttribute();

        if ($fqcn) {
            return $fqcn;
        }

        throw new Exception(
            'Provide a unit class by @covers annotation, #[CoversClass] attribute or getUnitClass() method.'
        );
    }

    /** @return null|class-string<UNIT> */
    private function getUnitClassByDocBlock(): ?string
    {
        try {
            $testCase        = new ReflectionClass($this);
            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock        = $docBlockFactory->create($testCase);
            $coversTag       = $docBlock->getTagsByName('covers')[0] ?? null;

            if ($coversTag instanceof Covers === false) {
                return null;
            }

            /** @var class-string<UNIT> $fqcn */
            $fqcn = (string)$coversTag->getReference();

            return $fqcn;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /** @return null|class-string<UNIT> */
    private function getUnitClassByAttribute(): ?string
    {
        $testCase        = new ReflectionClass($this);
        $coversAttribute = $testCase->getAttributes(CoversClass::class)[0] ?? null;

        if ($coversAttribute instanceof ReflectionAttribute === false) {
            return null;
        }

        /** @var class-string<UNIT> $fqcn */
        $fqcn = $coversAttribute->newInstance()->className();

        return $fqcn;
    }

    /**
     * @param ReflectionClass<UNIT> $unitReflection
     * @return array<int, mixed>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity))
     */
    private function buildParameters(ReflectionClass $unitReflection): array
    {
        $parameters               = $this->getUnitConstructorParameters();
        $parameterReflections     = $unitReflection->getConstructor()?->getParameters() ?? [];
        $parametersTypeCount      = [];
        $sortedParameters         = [];
        $missingBuiltinParameters = [];

        foreach ($parameterReflections as $parameterReflection) {
            /** @var ReflectionNamedType $parameterReflectionType */
            /** @var class-string<object> $parameterType */
            $parameterReflectionType = $parameterReflection->getType();
            $parameterType           = $parameterReflectionType->getName();

            $parametersTypeCount[$parameterType] = ($parametersTypeCount[$parameterType] ?? 0) + 1;
        }

        foreach ($parameterReflections as $parameterReflection) {
            /** @var ReflectionNamedType $parameterReflectionType */
            $parameterReflectionType = $parameterReflection->getType();
            $parameterName           = $parameterReflection->getName();

            /** @var class-string<object> $parameterType */
            $parameterType = $parameterReflectionType->getName();

            if (array_key_exists($parameterName, $parameters)) {
                if ($parameterReflection->isVariadic() === false || is_array($parameters[$parameterName]) === false) {
                    $sortedParameters[] = $parameters[$parameterName];

                    continue;
                }

                foreach ($parameters[$parameterName] as $item) {
                    $sortedParameters[] = $item;
                }

                continue;
            }

            if ($parameterReflectionType->isBuiltin() === true) {
                $missingBuiltinParameters[] = $parameterName;
                continue;
            }

            $mockId             = $parametersTypeCount[$parameterType] > 1 ? $parameterName : null;
            $sortedParameters[] = $this->mock($parameterType, $mockId);
        }

        if ($missingBuiltinParameters !== []) {
            throw new Exception(
                sprintf(
                    'Missing parameters for constructor of `%s`: %s',
                    $unitReflection->getName(),
                    implode(', ', $missingBuiltinParameters)
                )
            );
        }

        return $sortedParameters;
    }
}
