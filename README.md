# e3n Testing Unit Creator
The testing-unit-creator by e3n is a user-friendly tool designed to simplify the creation of units under test in your PHPUnit test cases.
Whether you're working with regular or abstract classes, this tool provides the flexibility to generate testable units efficiently.

## Features

- creates the unit under test
- mocks the dependencies of your unit
- provides access to the mocks
- supports abstract unit under test

## Installation

```bash
composer require e3n/testing-unit-creator --dev
```

## Getting started

### Use the `UnitCreatorTrait`
The features are provided by the `UnitCreatorTrait`. There are two different ways for using this trait.

#### 1) Implement your own abstract test case class (recommended)
Implement your own abstract TestCase class.
Your custom test case should use the `UnitCreatorTrait` and extend from PHPUnit's `TestCase`.

```php
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\TestCase;

/**
 * @template UNIT of object
 */
abstract class MyAbstractTestCase extends TestCase
{

    /** @use UnitCreatorTrait<UNIT> */
    use UnitCreatorTrait;
    
    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    ...
}
```

Your test cases can now easily extend from your abstract test case.
Provide the `@extends` annotation for code completion.

```php
/**
 * @extends MyAbstractTestCase<MyClass>
 */
class MyClassTest extends MyAbstractTestCase
{
}
```

#### 2) Use the UnitCreatorTrait in each test case
Instead of implementing an abstract test case for your project your test cases can use the `UnitCreatorTrait` directly.
We do not recommend this way due to bugs in the phpstorm's code completion.

```php
use e3n\Test\UnitCreatorTrait;
use PHPUnit\Framework\TestCase;

class MyClassTest extends TestCase
{

    /** @use UnitCreatorTrait<MyClass> */
    use UnitCreatorTrait;
    
    protected function tearDown(): void
    {
        $this->clearUnit();
    }

    ...
}
```

### Provide the class of your unit under test
There are tree ways to provide the class of your unit under test to the `UnitCreatorTrait`.

#### 1) `@covers` annotation
The `UnitCreatorTrait` uses the first `@covers` annotation of your test case for determining your unit under test.

```php
/**
 * @covers \Fully\Qualified\Class\Name\Of\MyClass
 * @extends MyAbstractTestCase<MyClass>
 */
class MyClassTest extends MyAbstractTestCase
{
}
```

#### 2) `CoversClass` attribute
The `UnitCreatorTrait` uses the first `CoversClass` attribute of your test case for determining your unit under test.

```php
/**
 * @extends MyAbstractTestCase<MyClass>
 */
#[CoversClass(MyClass::class)]
class MyClassTest extends MyAbstractTestCase
{
}
```

#### 3) `getUnitClass` method
You can implement the method `getUnitClass` to tell the `UnitCreatorTrait` the class of your unit under test.

```php
/**
 * @extends MyAbstractTestCase<MyClass>
 */
class MyClassTest extends MyAbstractTestCase
{
    /** @return class-string<MyClass> */
    protected function getUnitClass(): string
    {
        return MyClass::class;
    }
}
```

### Access your unit under test
#### 1) regular class

```php
class MyClassTest extends MyAbstractTestCase
{
    public function testMyMethod(): void
    {
        $unit   = $this->getUnit();
        $actual = $unit->myMethod();
        
        self::assertEquals('expectedValue', $actual);
    }
}
```

#### 2) abstract class
When testing an abstract class the `UnitCreatorTrait` provides a partial mock of your unit under test.
So you have the possibility to mock the behaviour of abstract methods.

```php
class MyClassTest extends MyAbstractTestCase
{
    public function testMyMethod(): void
    {
        $unit   = $this->getAbstractUnit();
        
        $unit->expects(self::once())
         ->method('abstractMethod')
         ->with('parameter')
         ->willReturn('expectedValue');
        
        $actual = $unit->myMethod();
        
        self::assertEquals('expectedValue', $actual);
    }
}
```

### Access mocked dependencies
When creating the unit under test the `UnitCreatorTrait` mocks the dependencies.
You can access those mocks by calling `$this->mock()`.

```php
class MyDependency
{
    public function methodA(): string
    {
        return 'lol';
    }
}

class MyClass
{
    public function __construct(private MyDependency $dependency)
    {
    }
    
    public function methodB(): string
    {
        return $this->dependency->methodA();
    }
}

class MyClassTest extends MyAbstractTestCase
{
    public function testMethodB(): void
    {
        $unit   = $this->getUnit();
        
        $this->mock(MyDependency::class)
            ->method('methodA')
            ->willReturn('rofl');
        
        $actual = $unit->methodB();
        
        self::assertEquals('rofl', $actual);
    }
}
```

### Provide builtin constructor parameters
When your unit under test requires some builtin parameters in the constructor you have to provide them by implementing the method `getUnitConstructorParameters`.

```php
class MyClass
{
    public function __construct(private string $a, private int $b)
    {
    }
}

class MyClassTest extends MyAbstractTestCase
{
    protected function getUnitConstructorParameters(): array
    {
        return [
            'a' => 'rofl mao',
            'b' => 1337,
        ];
    }
}
```
