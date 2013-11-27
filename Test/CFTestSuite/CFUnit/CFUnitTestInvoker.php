<?php
require_once ( 'CFTestCaseMethods.php' );
require_once ( realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'CFUnitTestConfig.php' );

class CFUnitTestInvoker
{
    private $testCases = array ( );
    private $rootFolder;
    protected $testCasseResults = array ( );
    
    public function __construct ( )
    {
        CFUnitTestConfig::Init ( );
        $this->rootFolder =  realpath(dirname(__FILE__)) . '/../../' . trim(CFUnitTestConfig::$TestFolder, '/');
    }
    
    protected function createTestCase ( $testCasePath, array $testMethods )
    {
        require_once ( $testCasePath );
        $testCase = basename ( $testCasePath, '.php' );
        $instance = new $testCase;
        
        $this->testCases[] = new CFTestCaseMethods ( $testCasePath, $instance, $testMethods );
    }
    
    public function addTestCase ( CFTestCaseMethods $testCase )
    {
        $this->testCases[] = $testCase;
    }
    
    public function addTestCases ( array $testCases )
    {
        $this->testCases = array_merge ( $this->testCases, $testCases );
    }
    
    protected function clearTestCases ( )
    {
        $this->testCases = array ( );
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
            
            $testCases[] = new CFTestCaseMethods ( $filepath, $instance, $testMethods );
        }
        
        return $testCases;
    }
    
    protected function executeTestMethods ( )
    {
        foreach ( $this->testCases as $testCaseMethods )
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