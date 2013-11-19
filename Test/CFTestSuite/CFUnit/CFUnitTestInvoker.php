<?php
require_once ( 'CFTestCaseMethods.php' );
require_once ( realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'CFUnitTestConfig.php' );

class CFUnitTestInvoker
{
    protected $rootFolder;
    protected $testCasseResults = array ( );
    
    public function __construct ( )
    {
        $this->rootFolder =  realpath(dirname(__FILE__)) . '/../../' . trim(CFUnitTestConfig::$TestFolder, '/');
    }
    
    protected function getAllTestCases ( )
    {
        $testCases = array ( );
        
        $directory = new RecursiveDirectoryIterator ( $this->rootFolder );
        $iterator = new RecursiveIteratorIterator ( $directory );
        $iterator->setFlags ( RecursiveDirectoryIterator::SKIP_DOTS );
        $regexIterator = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        
        foreach ( $regexIterator as $filepath => $object )
        {
            require_once ( $filepath );
            $testCase = basename ( $filepath, '.php' );

            $instance = new $testCase;
            $testMethods = preg_grep ( '/^Test_/i', get_class_methods ( $instance ) );
            
            $testCases[] = new CFTestCaseMethods ( $instance, $testMethods );
        }
        
        return $testCases;
    }
    
    protected function executeTestMethods ( array $testCases )
    {
        foreach ( $testCases as $testCaseMethods )
        {
            $testCaseMethods->TestCase->SetUpBeforeClass ( );
            foreach ( $testCaseMethods->TestMethods as $testMethod )
            {
                $testCaseMethods->TestCase->SetUp ( );
                $testCaseMethods->TestCase->$testMethod ( );
                $testCaseMethods->TestCase->TearDown ( );
            }
            $testCaseMethods->TestCase->TearDownAfterClass ( );
        }
        
    }
}

?>