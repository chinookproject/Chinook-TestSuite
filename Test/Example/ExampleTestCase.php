<?php
require_once('CFUnitTestCase.php');
require_once('../CFMock/CFMock.php');

class DummyClass
{
    public function SayMessage( $message )
    {
        echo 'I said: ' . $message;
    }
    
    public function RandomNumber ( )
    {
        return rand(0, 10);
    }
}

/**
 * This is an example test case that illustrates how an assertion can be made
 * Note that every method that needs to be tested should start with "Test_".
 */
class ExampleTestCase extends CFUnitTestCase
{
    /**
     * OPTIONAL - This method is run before each test method is called
     */
    public function SetUp() {}
    
    /**
     * OPTIONAL - This method is run after a test method is finished
     */
    public function TearDown() {}
    
    /**
     * OPTIONAL - This method is run when the test class is initialized
     */
    public function SetUpBeforeClass() {}
    
    /**
     * OPTIONAL - This method is run when the test class has run all test methods
     */
    public function TearDownAfterClass() {}
    
    /**
     * A simple test method with a fluent assertion
     */
    public function Test_A_simple_assertion()
    {
        $this->Assert(5)->Should()->Be(5)->And()->BeGreaterThan(2);
    }
    
    /**
     * A simple test method which shows how to Mock
     */
    public function Test_A_simple_mock_example()
    {
        //////////////////////////////////////////////////////////////////////
        // Arrange
        $message = 'something';
        $randomNumber = 5;
        $mock = new CFMock(new DummyClass());
        
        // This WILL be asserted
        $mock->ACallTo('RandomNumber')->Returns( $randomNumber )->ExpectOnce();
        
        // This will NOT be asserted because with a Return you don't define an Assert
        $mock->ACallTo('SayMessage')->Returns($message);
        
        //////////////////////////////////////////////////////////////////////
        // Act
        $resultMessage = $mock->SayMessage( 'Something random' );
        $reultNumber = $mock->RandomNumber ( );
        
        //////////////////////////////////////////////////////////////////////
        // Assert
        $this->Assert($resultMessage)->Should()->BeEquivalentTo( $message );
        $this->Assert($reultNumber)->Should()->BeEquivalentTo( $randomNumber );
    }
}

?>