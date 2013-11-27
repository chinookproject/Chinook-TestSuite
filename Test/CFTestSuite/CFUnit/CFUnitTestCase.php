<?php
require_once ( 'CFTestResult.php' );
require_once ( 'CFTestResultCollection.php' );

class CFUnitTestCase
{
    protected $currentTestCase;
    protected $methodChain = array ( );
    protected $totalAssertions = 0;
    protected $assertObject;
    
    public function SetUp ( ) { }
    public function TearDown ( ) { }
    public function SetUpBeforeClass ( ) { }
    public function TearDownAfterClass ( ) { }
    
    public function __call ( $name, $arguments )
    {
        if ( $name === 'And' )
        {
            $this->addMethodChain ( 'And' );
            return $this;
        }
    }
    
    protected function prepareAssertion ( )
    {       
        $callers = debug_backtrace ( );
        $testResult = new CFTestResult ( );
        $testResult->TestCase = basename($callers[1]['file'], '.php');
        $testResult->TestMethod = $callers[2]['function'];
        $this->currentTestCase = $testResult->TestCase;
        
        CFTestResultCollection::AddResult ( $testResult->TestCase, $testResult );
        
        $this->totalAssertions++;
    }
    
    protected function addResult ( $message, $result = false )
    {
        $testResult = CFTestResultCollection::GetResult ( $this->currentTestCase, $this->totalAssertions-1 );
        $testResult->Message = $message;
        $testResult->Result = $result;
    }
    
    protected function addMethodChain ( $method )
    {
        $result = CFTestResultCollection::GetResult ( $this->currentTestCase, $this->totalAssertions-1 );
        $result->MethodChain[] = $method;
    }
    
    public function Assert ( $object )
    {
        $this->prepareAssertion ( );
        
        $this->assertObject = $object;
        return $this;
    }
    
    public function Should ( )
    {
        $this->addMethodChain ( 'Should' );
        return $this;
    }
    
    /**
     * Compares the first object with the second object
     */
    public function Be ( $mixed )
    {
        
        if ( func_num_args ( ) > 0 )
        {
            if ( $this->assertObject !== $mixed )
            {
                if ( is_array ( $mixed ) && is_array ( $this->assertObject ) )
                {
                    $this->addResult ( 'Expected asserted object to have the exact same values in the array as was given, but there are differences', false );
                }
                else
                {
                    $this->addResult ( 'Expected asserted object to be ('.gettype($mixed).')[' . $mixed . '], but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
                }
            }
        }
        
        $this->addMethodChain ( "Be($mixed)" );
        return $this;
    }
    
    public function NotBe ( $mixed )
    {
        if ( func_num_args ( ) > 0 )
        {
            if ( $his->assertObject === $mixed )
            {
                $this->addResult ( 'Asserted object is ('.gettype($this->assertObject).')[' . $this->assertObject . '], expected not to be ('.gettype($mixed).')[' . $mixed . ']', false );
            }
        }
        
        $this->addMethodChain ( "NotBe($mixed)" );
        return $this;
    }
    
    public function BeAString ( )
    {
        if ( !is_string ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be of type (string), but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeAString()' );
        return $this;
    }
    
    public function BeAnObject ( )
    {
        if ( !is_object ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be of type (object), but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        $this->addMethodChain ( 'BeAnObject()' );
        return $this;
    }
    
    public function BeAnArray ( )
    {
        if ( !is_array ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be of type (array), but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeAnArray()' );
        return $this;
    }
    
    public function BeAFloat ( )
    {
        if ( !is_float ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be of type (float), but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeAFloat()' );
        return $this;
    }
    
    public function BeAnInt ( )
    {
        if ( !is_int ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be of type (int), but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeAnInt()' );
        return $this;
    }
    
    public function BeTrue ( )
    {
        if ( $this->assertObject !== true )
        {
            $this->addResult ( 'Expected asserted object to be TRUE, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeTrue()' );
        return $this;
    }
    
    public function BeFalse ( )
    {
        if ( $this->assertObject !== false )
        {
            $this->addResult ( 'Expected asserted object to be FALSE, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeFalse()' );
        return $this;
    }
    
    public function NotBeNull ( )
    {
        if ( $this->assertObject === null )
        {
            $this->addResult ( 'Expected asserted object not to be NULL, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'NotBeNull()' );
        return $this;
    }

    public function BeNull ( )
    {
        if ( $this->assertObject !== null )
        {
            $this->addResult ( 'Expected asserted object to be NULL, but ('.gettype($this->assertObject).')[' . print_r($this->assertObject, true) . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeNull()' );
        return $this;
    }
    
    public function BeEmpty ( )
    {
        if ( $this->assertObject !== '' || !is_string ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to be an EMPTY string, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeEmpty()' );
        return $this;
    }
    
    public function NotBeEmpty ( )
    {
        if ( $this->assertObject === '' || !is_string ( $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object not to be an EMPTY string, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'NotBeEmpty()' );
        return $this;
    }
    
    public function HaveLength ( $length )
    {
        if ( strlen ( $this->assertObject ) !== $length )
        {
            $this->addResult ( 'Expected asserted object to have a length of ['.$length.'], but found a length of ['. strlen($this->assertObject) . ']', false );
        }
        
        $this->addMethodChain ( 'HaveLength()' );
        return $this;
    }

    /**
     * A case insensitive string compare
     */
    public function BeEquivalentTo ( $string )
    {
        if ( strcasecmp ( $this->assertObject, $string ) )
        {
            $this->addResult ( 'Expected asserted object to be [' . $string . '], but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( "BeEquivalentTo($string)" );
        return $this;
    }
    
    public function EndWith ( $string )
    {
        if ( !preg_match ( '~'.preg_quote($string).'$~', $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to end with string [' . $string . '], but couldn\'t find match in given string: ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "EndWith($string)" );
        return $this;
    }

    public function EndWithEquivalent ( $string )
    {
        if ( !preg_match ( '~'.preg_quote($string).'$~i', $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to end with string [' . $string . '], but couldn\'t find match in given string: ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "EndWithEquivalent($string)" );
        return $this;
    }
    
    /**
     * String, Array
     */
    public function Contain ( $mixed )
    {
        if ( is_array ( $mixed ) && is_array ( $this->assertObject ) )
        {
            if ( !array_intersect ( $this->assertObject, $mixed ) )
            {
                $this->addResult ( 'Expected asserted object to contain values that exist in given array, but couldn\'t find any matching values', false );
            }
        }
        else if ( is_string ( $mixed ) && is_string ( $this->assertObject ) )
        {
            if ( strpos ( $this->assertObject, $mixed ) === false )
            {
                $this->addResult ( 'Expected asserted object to contain string [' . $mixed . '], but couldn\'t find a match in given string: [' . $this->assertObject . ']', false );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object to contain what given object contains, but a type mismatch was found. AssertObject: ('.gettype($this->assertObject).'), given: ('.gettype($mixed).')', false );
        }
        
        $this->addMethodChain ( "Contain($mixed)" );
        return $this;
    }
    
    /**
     * String, Array
     */
    public function NotContain ( $mixed )
    {
        if ( is_array ( $mixed ) && is_array ( $this->assertObject ) )
        {
            if ( array_intersect ( $this->assertObject, $mixed ) )
            {
                $this->addResult ( 'Expected asserted object not to contain values that exist in given array, but found a match', false );
            }
        }
        else if ( is_string ( $mixed ) && is_string ( $this->assertObject ) )
        {
            if ( strpos ( $this->assertObject, $mixed ) !== false )
            {
                $this->addResult ( 'Expected asserted object not to contain string [' . $mixed . '], but found a match in given string: [' . $this->assertObject . ']', false );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object to contain what given object contains, but a type mismatch was found. AssertObject: ('.gettype($this->assertObject).'), given: ('.gettype($mixed).')', false );
        }
        
        $this->addMethodChain ( "NotContain($mixed)" );
        return $this;
    }
    
    public function ContainEquivalentOf ( $mixed )
    {
        if ( is_array ( $mixed ) && is_array ( $this->assertObject ) )
        {
            if ( !array_intersect ( array_map ( 'strtolower', $this->assertObject ), array_map ( 'strtolower', $mixed ) ) )
            {
                $this->addResult ( 'Expected asserted object not to contain values that exist in given array, but found a match', false );
            }
        }
        else if ( is_string ( $mixed ) && is_string ( $this->assertObject ) )
        {
            if ( stripos ( $this->assertObject, $mixed ) === false )
            {
                $this->addResult ( 'Expected asserted object to contain string [' . $mixed . '], but couldn\'t find a match in given string: [' . $this->assertObject . ']', false );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object to contain what given object contains, but a type mismatch was found. AssertObject: ('.gettype($this->assertObject).'), given: ('.gettype($mixed).')', false );
        }
        
        $this->addMethodChain ( "ContainEquivalentOf($mixed)" );
        return $this;
    }
    
    public function NotContainEquivalentOf ( $mixed )
    {
        if ( is_array ( $mixed ) && is_array ( $this->assertObject ) )
        {
            if ( array_intersect ( array_map ( 'strtolower', $this->assertObject ), array_map ( 'strtolower', $mixed ) ) )
            {
                $this->addResult ( 'Expected asserted object not to contain values that exist in given array, but found a match', false );
            }
        }
        else if ( is_string ( $mixed ) && is_string ( $this->assertObject ) )
        {
            if ( stripos ( $this->assertObject, $mixed ) !== false )
            {
                $this->addResult ( 'Expected asserted object not to contain string [' . $mixed . '], but found match in given string: ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object to contain what given object contains, but a type mismatch was found. AssertObject: ('.gettype($this->assertObject).'), given: ('.gettype($mixed).')', false );
        }
        
        $this->addMethodChain ( "NotContainEquivalentOf($mixed)" );
        return $this;
    }
    
    public function StartWith ( $string )
    {
        if ( !preg_match ( '~^'.preg_quote($string).'~', $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to start with string [' . $string . '], but couldn\'t find match in given string: [' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "StartWith($string)" );
        return $this;
    }

    public function StartWithEquivalent ( $string )
    {
        if ( !preg_match ( '~^'.preg_quote($string).'~i', $this->assertObject ) )
        {
            $this->addResult ( 'Expected asserted object to start with string [' . $string . '], but couldn\'t find match in given string: [' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "StartWithEquivalent($string)" );
        return $this;
    }
    
    public function BeGreaterOrEqualTo ( $number )
    {
        if ( $this->assertObject < $number )
        {
            $this->addResult ( 'Expected asserted object to be greater than or equal to ['.$number.'], but found ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "BeGreaterOrEqualTo($number)" );
        return $this;
    }
    
    public function BeGreaterThan ( $number )
    {
        if ( $this->assertObject <= $number )
        {
            $this->addResult ( 'Expected asserted object to be greater than ['.$number.'], but found ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "BeGreaterThan($number)" );
        return $this;
    }
    
    public function BeLessOrEqualTo ( $number )
    {
        if ( $this->assertObject > $number )
        {
            $this->addResult ( 'Expected asserted object to be less than or equal to ['.$number.'], but found ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "BeLessOrEqualTo($number)" );
        return $this;
    }
    
    public function BeLessThan ( $number )
    {
        if ( $this->assertObject >= $number )
        {
            $this->addResult ( 'Expected asserted object to be less than ['.$number.'], but found ('.gettype($this->assertObject).')[' . $this->assertObject . ']', false );
        }
        
        $this->addMethodChain ( "BeLessThan($number)" );
        return $this;
    }
    
    public function BePositive ( )
    {
        if ( $this->assertObject <= 0 )
        {
            $this->addResult ( 'Expected asserted object to be a positive number, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BePositive()' );
        return $this;
    }
    
    public function BeNegative ( )
    {
        if ( $this->assertObject >= 0 )
        {
            $this->addResult ( 'Expected asserted object to be a negative number, but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given', false );
        }
        
        $this->addMethodChain ( 'BeNegative()' );
        return $this;
    }

    public function BeInRange ( $min, $max )
    {
        if ( $this->assertObject < $min || $this->assertObject > $max )
        {
            $this->addResult ( 'Expected asserted object to be in the range of min: '.$min.' and max: '.$max.', but ('.gettype($this->assertObject).')[' . $this->assertObject . '] was given' );
        }
        
        $this->addMethodChain ( 'BeInRange()' );
        return $this;
    }
    
    
    
    public function BeAfter ( $datetime )
    {
        if ( gettype ( $datetime ) === 'object' && gettype ( $this->assertObject ) === 'object' )
        {
            if ( $this->assertObject->getTimestamp ( ) <= $datetime->getTimestamp ( ) )
            {
                $this->addResult ( 'Expected asserted object to be after date ['.$datetime->format('Y-m-d H:i').'], but ('.gettype($this->assertObject).')[' . $this->assertObject->format('Y-m-d H:i') . '] was given' );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object and given object to be of type DateTime, but a mismatch was found. AssetObject: ('.gettype($this->assertObject).') -> ('.gettype($datetime).')' );
        }
        
        $this->addMethodChain ( 'BeAfter()' );
        return $this;
    }
    
    public function BeBefore ( $datetime )
    {
        if ( gettype ( $datetime ) === 'object' && gettype ( $this->assertObject ) === 'object' )
        {
            if ( $this->assertObject->getTimestamp ( ) >= $datetime->getTimestamp ( ) )
            {
                $this->addResult ( 'Expected asserted object to be before date ['.$datetime->format('Y-m-d H:i').'], but ('.gettype($this->assertObject).')[' . $this->assertObject->format('Y-m-d H:i') . '] was given' );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object and given object to be of type DateTime, but a mismatch was found. AssetObject: ('.gettype($this->assertObject).') -> ('.gettype($datetime).')' );
        }
        
        $this->addMethodChain ( 'BeBefore()' );
        return $this;
    }
    
    public function BeOnOrAfter ( $datetime )
    {
        if ( gettype ( $datetime ) === 'object' && gettype ( $this->assertObject ) === 'object' )
        {
            if ( $this->assertObject->getTimestamp ( ) < $datetime->getTimestamp ( ) )
            {
                $this->addResult ( 'Expected asserted object to be on or after date ['.$datetime->format('Y-m-d H:i').'], but ('.gettype($this->assertObject).')[' . $this->assertObject->format('Y-m-d H:i') . '] was given' );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object and given object to be of type DateTime, but a mismatch was found. AssetObject: ('.gettype($this->assertObject).') -> ('.gettype($datetime).')' );
        }
        
        $this->addMethodChain ( 'BeOnOrAfter()' );
        return $this;
    }
    
    public function BeOnOrBefore ( $datetime )
    {
        if ( gettype ( $datetime ) === 'object' && gettype ( $this->assertObject ) === 'object' )
        {
            if ( $this->assertObject->getTimestamp ( ) > $datetime->getTimestamp ( ) )
            {
                $this->addResult ( 'Expected asserted object to be on or before date ['.$datetime->format('Y-m-d H:i').'], but ('.gettype($this->assertObject).')[' . $this->assertObject->format('Y-m-d H:i') . '] was given' );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object and given object to be of type DateTime, but a mismatch was found. AssetObject: ('.gettype($this->assertObject).') -> ('.gettype($datetime).')' );
        }
        
        $this->addMethodChain ( 'BeOnOrBefore()' );
        return $this;
    }

//    public function HaveDay ( )
//    { }
//    
//    public function HaveMonth ( )
//    { }
//    
//    public function HaveYear ( )
//    { }
//    
//    public function HaveHour ( )
//    { }
//    
//    public function HaveMinute ( )
//    { }
//    
//    public function HaveSecond ( )
//    { }
    
    
    public function NotContainNull ( )
    {
        if ( is_array ( $this->assertObject ) )
        {
            if ( in_array ( null, $this->assertObject ) )
            {
                $this->addResult ( 'Expected asserted object not to contain NULL values in array, but found a match' );
            }
        }
        else
        {
            $this->addResult ( 'Expected asserted object to be of type (array), but ('.gettype($this->assertObject).') was found' );
        }
        
        $this->addMethodChain ( 'NotContainNull()' );
        return $this;
    }
    
    private $exceptionMessage;
    public function ShouldThrow ( $func )
    {
        $hasThrown = false;
        try
        {
            $func ( );
        }
        catch(Exception $e)
        {
            $this->exceptionMessage = $e->getMessage();
            $hasThrown = true;
        }

        if ( !$hasThrown )
        {
            $this->addResult ( 'Expected to throw an exception, but nothing happend' );
        }
        
        $this->addMethodChain ( 'ShouldThrow()' );
        return $this;
    }
    
    public function WithMessage ( $message )
    {
        $messagePattern = str_replace ( '*', '(.*?)', $message );
        if ( !preg_match ( '~^' . $messagePattern . '$~', $this->exceptionMessage ) )
        {
            $this->addResult ( 'Expected exception to be thrown with message: "' . $message . '", but "' . $this->exceptionMessage . '" was thrown instead' );
        }
        
        $this->addMethodChain ( "WithMessage($message)" );
        return $this;
    }
    
    public function ShouldNotThrow ( $func )
    {
        try
        {
            $func ( );
        }
        catch(Exception $e)
        {
            $this->addResult ( 'Expected not to throw an exception, but exception was thrown with message: ' . $e->getMessage() );
        }
        
        $this->addMethodChain ( 'ShouldNotThrow()' );
        return $this;
    }
}

?>