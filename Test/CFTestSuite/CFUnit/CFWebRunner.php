<?php
ini_set('display_errors', '1');     # don't show any errors...
error_reporting(E_ALL | E_STRICT);  # ...but do log them

require_once ( 'CFUnitTestInvoker.php' );
require_once ( 'CFTestResultCollection.php' );

class CFWebRunner extends CFUnitTestInvoker
{
    public $Cases;
    public static $Errors = array ( );
    
    public function __construct ( )
    {
        parent::__construct ( );
        
        $this->Cases = $this->getAllTestCases ( );
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $this->CreateSelectedTests ( );
            $this->executeTestMethods ( );
        }
        else
        {
            $this->addTestCases ( $this->Cases );
            $this->executeTestMethods ( );
        }
        
        $this->testCasseResults = CFTestResultCollection::GetResults ( );
        
        require_once ( 'WebUnitTestLoggerViews/result.html' );
    }
    
    public function CreateSelectedTests ( )
    {
        if ( isset ( $_POST['testmethods'] ) )
        {
            foreach ( $_POST['testmethods'] as $testCase => $testMethods )
            {
                $this->createTestCase ( $testCase, $testMethods );
            }
        }
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

class Error
{
    public $ErrorNumber;
    public $ErrorString;
    public $ErrorFile;
    public $ErrorLine;
    public $ErrorMessage;
}

function CustomErrorHandler ( $errno, $errstr, $errfile, $errline )
{    
    if (! (error_reporting() & $errno) )
    {
        // This error code is not included in error_reporting
        return;
    }
    
    //echo '<div class="container">';
    //echo '<div class="alert alert-danger" style="margin:0px; padding:5px;">';

    switch ($errno) {
    case E_USER_ERROR:
        $error = new Error();
        $error->ErrorNumber = $errno;
        $error->ErrorString = $errstr;
        $error->ErrorFile = $errfile;
        $error->ErrorLine = $errfile;
        $error->ErrorMessage = "<strong>ERROR</strong> $errno <br />\n
        Fatal error on line $errline in file $errfile";
        CFWebRunner::$Errors[] = $error;
        break;

    case E_USER_WARNING:
        $error = new Error();
        $error->ErrorNumber = $errno;
        $error->ErrorString = $errstr;
        $error->ErrorFile = $errfile;
        $error->ErrorLine = $errfile;
        $error->ErrorMessage = "<strong>Warning</strong> $errno <br />\n
        Fatal error on line $errline in file $errfile";
        CFWebRunner::$Errors[] = $error;
        break;

    case E_USER_NOTICE:
        $error = new Error();
        $error->ErrorNumber = $errno;
        $error->ErrorString = $errstr;
        $error->ErrorFile = $errfile;
        $error->ErrorLine = $errfile;
        $error->ErrorMessage = "<strong>Notice</strong> $errno <br />\n
        Fatal error on line $errline in file $errfile";
        CFWebRunner::$Errors[] = $error;
        break;

    default:
        $error = new Error();
        $error->ErrorNumber = $errno;
        $error->ErrorString = $errstr;
        $error->ErrorFile = $errfile;
        $error->ErrorLine = $errfile;
        $error->ErrorMessage = "<strong>Unknown Error</strong> $errno <br />\n
        Fatal error on line $errline in file $errfile";
        CFWebRunner::$Errors[] = $error;
        break;
    }
    
    //echo '</div>';
    //echo '</div>';

    /* Don't execute PHP internal error handler */
    return true;
}

set_error_handler("CustomErrorHandler");

$now = microtime(true);
$run = new CFWebRunner ( );
$now = microtime(true) - $now;
echo "This test was run in $now seconds";


?>