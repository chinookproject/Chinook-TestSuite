<?php

class CFTestResultCollection
{
    private static $results = array ( );
    
    public static function AddResult ( $testCaseName, CFTestResult $result )
    {
        self::$results[$testCaseName][] = $result;
    }
    
    public static function RemoveResult ( $testCaseName, $index )
    {
        unset ( self::$results[$testCaseName][$index] );
    }
    
    public static function &GetResult ( $testCaseName, $index )
    {
        return self::$results[$testCaseName][$index];
    }
    
    public static function GetResults ( )
    {
        /*
        echo '<pre>';
        print_r(self::$results);
        echo '</pre>';
        die();*/
        return self::$results;
    }
}

?>