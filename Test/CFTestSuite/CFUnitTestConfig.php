<?php
require_once ( '../../../Core/CFBootstrap.php' );

class CFUnitTestConfig
{
    public static $TestFolder = '/UnitTests';
    
    // Initialize yor spl_autoload for example, so your classes/interfaces can still be 
    // loaded automatically
    public static function Init ( )
    {
        // A place to load custom code like an autoloader class
    }
}

?>
