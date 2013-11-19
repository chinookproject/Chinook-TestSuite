<?php

class CFTestCaseMethods
{
    public $TestCase;
    public $TestMethods = array ( );
    
    public function __construct ( &$testCase, array $testMethods )
    {
        $this->TestCase = $testCase;
        $this->TestMethods = $testMethods;
    }
}

?>