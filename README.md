Chinook-TestSuite
=================

Chinook Unit Test &amp; Mocking Framework

Installation &amp; Configuration
============

1. Download the **"TestSuite"** folder and put it in any directory you like.
2. Go into the **TestSuite** folder and open the file **CFUnitTestConfig.php**.
3. Change the **$TestFolder** path to the folder where your unit tests will reside in.

Note: The **$TestFolder** path will start looking from the parent of the **TestSuite** folder.
So if your TestSuite folder is in the ROOT of your web server, it will start looking from the ROOT
directory. If your TestSuite folder is in a sub folder called "Tests" for example. It will then start looking in
the "Tests" folder.

**Example**
  - ROOT
    - TestSuite
    - UnitTests

Then **$TestFolder** must be "UnitTests"

---

  - ROOT
    - SubFolder1
      - TestSuite
    - UnitTests
      - UnitTest1.php

Then **$TestFolder** must be "../UnitTests"

Creating a Unit Test
====================

A Unit Test class (or Test Case) can have any name and must always extend from **CFUnitTestCase** and therfor 
this class must be included. It is important that the class name of the Unit Test is the same as the file name.

A test method MUST have **Test_** as prefix. All other methods will not be run by the Test Suite.

<pre>
class ExampleTestCase extends CFUnitTestCase
{
    public function Test_A_simple_assertion()
    {
        $this->Assert(5)->Should()->Be(5)->And()->BeGreaterThan(2);
    }
}
</pre>

Additionally a Test Case can also have one of the following methods:

<pre>
public function SetUp() - This is run before each test method is executed
public functin TearDown() - This is run at the end of each test method
public function SetUpBeforeClass() - This is run before any test method is executed
public function TearDownAfterClass() - This is run after all test methods are executed
</pre>

For a more detailed example (including Mocking) check the "Example" folder.

Fluent Assertions
==========

An assertion can be made in a fluent way. The following assertions are supported.

<pre>
// Mixed assertion
$this->Assert($string)->Should()->Be('something');
$this->Assert($array)->Should()->Be($someOtherArray);
$this->Assert($bool)->Should()->NotBe(true);

// Type checking
$this->Assert($string)->Should()->BeAString();
$this->Assert($object)->Should()->BeAnObject();
$this->Assert($array)->Should()->BeAnArray();
$this->Assert($int)->Should()->BeAnInt();
$this->Assert($float)->Should()->BeAFloat();
$this->Assert($bool)->Should()->BeTrue();
$this->Assert($bool)->Should()->BeFalse();
$this->Assert($mixed)->Should()->NotBeNull();
$this->Assert($mixed)->Should()->BeNull()
$this->Assert($string)->Should()->BeEmpty();
$this->Assert($string)->Should()->NotBeEmpty();

// String specific assertions
$this->Assert($string)->Should()->HaveLength(5);
$this->Assert($string)->Should()->BeEquivalentTo($someString); // Case insensitive compare
$this->Assert($string)->Should()->StartsWith($someString); // Case sensitive compare
$this->Assert($string)->Should()->StartsWithEquivalent($someString); // Case insensitive compare
$this->Assert($string)->Should()->EndWith($someString); // Case sensitive compare
$this->Assert($string)->Should()->EndWithEquivalent($someString); // Case insensitive compare
$this->Assert($string)->Should()->Contain($someText);
$this->Assert($string)->Should()->NotContain($someText);
$this->Assert($string)->Should()->ContainEquivalentOf($someString); // Case insensitive compare (also on array values)
$this->Assert($string)->Should()->NotContainEquivalentOf($someString); // Case insensitive compare (also on array values)

// Array specific assertions
$this->Assert($array)->Should()->Contain($someOtherArray); // On intersect = success
$this->Assert($array)->Should()->NotContain($someOtherArray); // When not intersects = success
$this->Assert($array)->Should()->NotContainNull();

// Number assertions
$this->Assert($int)->Should()->BeGreaterOrEqualTo($number);
$this->Assert($int)->Should()->BeGreaterThan($number);
$this->Assert($int)->Should()->BeLessOrEqualTo($number);
$this->Assert($int)->Should()->BeLessThan($number);
$this->Assert($int)->Should()->BePositive();
$this->Assert($int)->Should()->BeNegative();
$this->Assert($int)->Should()->BeInRange($min, $max); //min=1, max=2 and given=2 will result in success

// Date assertions
$this->Assert($datetime)->Should()->BeAfter($someDatetime);
$this->Assert($datetime)->Should()->BeBefore($someDatetime);
$this->Assert($datetime)->Should()->BeOnOrAfter($someDatetime);
$this->Assert($datetime)->Should()->BeOnOrBefore($someDatetime);

// Throwable assertions
$this->Assert()->Should()->ShouldThrow($func); Give the method that should be executed as an anonymous function to this method.
$this->Assert()->Should()->ShouldThrow($func)->WithMessage('Exact exception message');
$this->Assert()->Should()->ShouldThrow($func)->WithMessage('* psrtial message'); // The asterisk acts as a wild card. Can be used at the beginning, end or both sides of the string
$this->Assert()->Should()->ShouldNotThrow($func);


// Extending assertions with "And()"
$this->Assert($string)->Should()->BeAString()->And()->HaveLength(5);
</pre>


Mocking
=======

When you want to mock an object then you must first include the class **CFMock/CFMock.php**. Now mocking will be
a breeze.

You create a mocked version of an object like this:

<pre>
$mock = new CFMock::Create( 'DummyClass' );
$mock->ACallTo('SomeMethod')->Returns('some value');

// Or even namespaced classes can be loaded:
$mock = new CFMock::Create( '\Some\Namespace\DummyClass' );
</pre>

Calls can then be made on the **$mock** object.

The mock framework also comes with a few assertions.

<pre>
ExpectCallCount(2); // Expects that many calls to a certain method
ExpectMinimumCallCount(2); // Expects at least that many calls to a certain method
ExpectMaximumCallCount(2); // Expects a maximum of 2 calls to a method, less is fine as well
ExpectNever(); // A certain method should never be called
ExpectOnce(); // Only a single call is expected to be made to a certain method
</pre>

A typical setup for a mock test could be this:

<pre>
$mock = new CFMock::Create( 'DummyClass' );
$mock->ACallTo('SomeMethod')->Returns('some value')->ExpectOnce();

$mock->SomeMethod('message'); // Will return the string: 'some value'
</pre>

This is obviously not a real world example, but it should illustrate the idea.

Run Unit Tests
==============

When you have created your unit tests then these can easily be run from the browser. Simply browse to **"TestSuit/CFunit/CFWebRunner.php"**

In there you can simply hit the "Run Tests" button or select the tests you want to run.

Screenshot
==========

![Example test with selected tests](http://i.imgur.com/5lC8o2f.png)
