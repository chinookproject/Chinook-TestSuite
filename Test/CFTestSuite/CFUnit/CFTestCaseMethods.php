<?php

class CFTestCaseMethods
{
    public $TestCasePath;
    public $TestCase;
    public $TestMethods = array ( );
    
    /**
     * CFTestCaseMethods::__construct()
     * 
     * @param string $testCasePath Full path to the test case
     * @param object $testCase Object reference to an instance of the test case
     * @param array $testMethods An array of strings holding the names of all test methods in this test case 
     * @return void
     */
    public function __construct ( $testCasePath, &$testCase, array $testMethods )
    {
        $this->TestCasePath = $testCasePath;
        $this->TestCase = $testCase;
        $this->TestMethods = $testMethods;
    }
}

?>