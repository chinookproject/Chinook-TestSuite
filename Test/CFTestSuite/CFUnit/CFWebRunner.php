<?php
ini_set('display_errors', '1');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

require_once ( 'CFUnitTestInvoker.php' );
require_once ( 'CFTestResultCollection.php' );

class CFWebRunner extends CFUnitTestInvoker
{
    public function __construct ( )
    {
        parent::__construct ( );
                
        $cases = $this->getAllTestCases ( );
        $this->executeTestMethods ( $cases );
        
        $this->testCasseResults = CFTestResultCollection::GetResults ( );
        
        require_once ( 'WebUnitTestLoggerViews/result.html' );
    }
        
    public function GetSucceededResultCount ( array $testCaseResults )
    {
        $successCount = 0;
        foreach ( $testCaseResults as $result )
        {
            if ( $result->Result === true )
            {
                $successCount++;
            }
        }
        
        return $successCount;
    }
}

$now = microtime(true);
$run = new CFWebRunner ( );
$now = microtime(true) - $now;
echo "This test was run in $now seconds";


?>