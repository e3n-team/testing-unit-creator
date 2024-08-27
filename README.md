# e3n Testing Unit Creator
>The testing-unit-creator by e3n is a user-friendly tool designed 
to simplify the creation of testing units for PHP classes. Whether you're working 
with regular or abstract classes, this tool provides the flexibility to generate 
testable units efficiently, making your testing process more versatile and streamlined.


## Installation

```
composer require e3n/testing-unit-creator --dev
```

## Getting started
The testing-unit-creator offers a flexible approach to creating testing units 
for your PHP classes. Depending on your preference and the structure 
of your tests, you can create units either by using attributes 
or by overriding method getUnitClass(). Our recommended approach 
is to implement a custom TestCase that allows you to create a unit via an annotation

## Recommended Way
We recommend implementing your own custom TestCase to fully
leverage the testing-unit-creator package.
This allows you to create testable units in a consistent and efficient manner.
Your custom TestCase should extend from PHPUnit's TestCase, KernelTestCase, or any other base test class that
suits your project’s needs. It's important to implement the TestCase with the following structure:

```
/**
 * @template UNIT of object
 */
class MyTestCase extends TestCase
{

    /** @use UnitCreatorTrait<UNIT> */
    use UnitCreatorTrait;

    ...

}
```
In this example, the @template annotation defines a type template for the unit being tested, 
ensuring strong typing and improved IDE autocompletion. 
This setup ensures that your custom TestCase is fully compatible with the 
testing-unit-creator package, allowing you to create testable units 
in a streamlined and efficient way.

## Creating and Passing Units in Your Test Class
To effectively test units in your project using the testing-unit-creator package, 
you need to create and manage instances of the unit being tested within your test classes. 
The package provides a streamlined way to do this, either through attributes or methods, 
ensuring that your tests are consistent and maintainable.

### Passing Unit By Attribute
One way to specify the unit class directly in your test class is by using attributes.
This approach allows you to define the class being tested and any related
configuration right within the class declaration.
```
#[CoversClass(MyClass::class)]
#[CoversClass(UnitCreatorTrait::class)]
#[Group('unit')]
class MyClassTest extends MyTestCase
{
```
### Passing Unit By Method
Alternatively, you can dynamically specify the unit class by implementing the getUnitClass 
method within your test class. This method should return the class name of the unit you intend to test.
```
class MyClassTest extends MyTestCase
{

...

/** @return class-string<MyClass> */
protected function getUnitClass(): string
{
    return MyClass::class;
}
```
### Passing Unit By ...


## Creating and Accessing the Unit
Once the unit class is defined, whether by attribute or method, you can create and access an instance of 
it within your test methods using the getUnit() method provided by the UnitCreatorTrait.
### Basic Unit Creation
```
public function testMyMethod(): void
{
    $unit = $this->getUnit();
    
    $result = $unit->someMethod();
    
    $this->assertEquals('expectedValue', $result);
}
```
The getUnit() method automatically instantiates the unit class based on your configuration. 
This ensures that the unit is ready to be tested without the need for manual setup, making your tests more efficient 
and easier to maintain.

### Creating abstract unit
In some cases, you might need to work with abstract classes. 
The testing-unit-creator package allows you to instantiate abstract units by using the getAbstractUnit() method.
```
public function testMyMethod(): void
{
    $unit          = $this->getAbstractUnit();
    $expectedValue = 'Test';

    $unit->expects(self::once())
         ->method('abstractMethod')
         ->with('TestArgument')
         ->willReturn($expectedValue);

    $result = $unit->callAbstractMethod('TestArgument');

    self::assertEquals($expectedValue, $result);
}
```

### Creating Mocks
Mocks are essential for isolating the unit under test from its dependencies. The testing-unit-creator package provides 
an easy way to create mocks of classes, which you can then use to simulate the behavior of dependencies
```

public function testMockExpectation(): void
{
    $this->mock(ClassName::class)
         ->expects(self::once())
         ->method('methodName')
         ->willReturn('B');

    $actual = $this->getUnit()->callMethod();

    self::assertSame('B', $actual);
}
```



