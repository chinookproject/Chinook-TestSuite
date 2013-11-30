
require_once ( 'CFFixture.php' );
require_once ( '../CFUnit/CFTestResult.php' );

require_once ( '{mock_file}' );

class CFMockedObject {mock_class}
{
    protected $fixtures = array ( );
    protected $mockInstance;
    protected $testIndex = 0;
    protected $currentTestCase;
    protected $testResults = array ( );
    
    // Mock
    protected $methodName;
    protected $args;
    
    public function __construct ( $instance )
    {
        $this->mockInstance = $instance;
    }
    
    public function __destruct ( )
    {        
        foreach ( $this->fixtures as $key => $fixture )
        {
            $called = 0;
            $called = $fixture->Called;
            
            if ( ($fixture->MaximumCallCount !== null && $fixture->Called > $fixture->MaximumCallCount) )
            {
                $message = 'Expected '.$fixture->MaximumCallCount.' call(s) to be made, but '.$called.' calls were made.';
                $this->testResults[$key]->Message = $message;
                $this->testResults[$key]->Result = false;
            }
            else if ( $fixture->MinimumCallCount !== null && $fixture->Called < $fixture->MinimumCallCount )
            {
                $message = 'Expected '.$fixture->MinimumCallCount.' call(s) to be made, but '.$called.' calls were made.';
                $this->testResults[$key]->Message = $message;
                $this->testResults[$key]->Result = false;
            }
            else if ( $fixture->MinimumCallCount === null && $fixture->MaximumCallCount === null )
            {
                // dont create a message, because there is nothing to assert
                unset ( $this->testResults[$key] );
            }
            else
            {
                // nothing
            }
        }
        
        foreach ( $this->testResults as $result )
        {
            CFTestResultCollection::AddResult ( $result->TestCase, $result );
        }
    }
    
    protected function resolveMatchingMethod ( $methodName, $args )
    {
        $matchingFixture = null;
        
        foreach ( $this->fixtures as $fixture )
        {
            if ( $fixture->Method !== $methodName )
                continue;
            
            if ( empty ( $fixture->ParamMatches ) || array_intersect ( $fixture->ParamMatches, $args ) )
            {
                $matchingFixture = $fixture; 
                break;
            }
        }
        
        return $matchingFixture;
    }
    
    protected function &getLastFixture ( )
    {
        $lastIndex = count ( $this->fixtures ) -1;
        return $this->fixtures[$lastIndex];
    }
    
    protected function addResult ( $index, $message, $result )
    {
        $this->testResults[$index]->Message = $message;
        $this->testResults[$index]->Result = $result;
    }
    
    protected function addMethodChain ( $method )
    {
        $this->testResults[$this->testIndex-1]->MethodChain[] = $method;
    }
    
    public function __call ( $methodName, $args )
    {
        if ( !method_exists ( $this->mockInstance, $methodName ) )
        {
            throw new \Exception("Method '$methodName' doesn't exist on object");
        }
        
        $matchingFixture = $this->resolveMatchingMethod ( $methodName, $args );
        
        if ( $matchingFixture !== null )
        {
            $this->methodName = $methodName;
            $this->args = $args;
            
            $matchingFixture->Called++;
            return $matchingFixture->Returns;
        }
    }
    
    protected function prepareAssertion ( )
    {
        $testResult = new CFTestResult ( );
        
        $callers = debug_backtrace ( );
        
        // no assertions
        $testResult->TestCase = basename($callers[0]['file'], '.php');
        $testResult->TestMethod = $callers[1]['function'];
        
        // with assertions
        if ( strstr ( $testResult->TestCase, 'CFMock' ) )
        {
            $testResult->TestCase = basename($callers[1]['file'], '.php');
            $testResult->TestMethod = $callers[2]['function'];
        }
        
        $this->currentTestCase = $testResult->TestCase;
        $this->testIndex++;
        
        $this->testResults[] = $testResult;
    }
    
    public function ACallTo ( $methodName, array $paramMatches = array() )
    {
        $this->prepareAssertion ( );
        
        $fixture = new CFFixture();
        $fixture->Method = $methodName;
        $fixture->ParamMatches = $paramMatches;
        
        $this->fixtures[] = $fixture;
        
        $this->addMethodChain ( "ACallTo('$methodName')" );
        
        return $this;
    }
    
    public function Returns ( $returns )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->Returns = $returns;
        
        $this->addMethodChain ( "Returns('$returns')" );
        
        return $this;
    }
    
    public function ExpectCallCount ( $amount )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->MinimumCallCount = $amount;
        $fixture->MaximumCallCount = $amount;
        
        $this->addMethodChain ( "ExpectedCallCount($amount)" );
        
        return $this;
    }
    
    public function ExpectMinimumCallCount ( $amount )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->MinimumCallCount = $amount;
        
        $this->addMethodChain ( 'ExpectedMinimumCallCount' );
        
        return $this;
    }
    
    public function ExpectMaximumCallCount ( $amount )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->MaximumCallCount = $amount;
        
        $this->addMethodChain ( 'ExpectedMaximumCallCount' );
        
        return $this;
    }
    
    public function ExpectNever ( )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->MinimumCallCount = 0;
        $fixture->MaximumCallCount = 0;
        
        $this->addMethodChain ( 'ExpectedNever' );
        
        return $this;
    }
    
    public function ExpectOnce ( )
    {
        $fixture = $this->getLastFixture ( );
        $fixture->MinimumCallCount = 1;
        $fixture->MaximumCallCount = 1;
        
        $this->addMethodChain ( 'ExpectedOnce' );
        
        return $this;
    }
}

