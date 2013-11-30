<?php
require_once ( 'CFFixture.php' );
require_once ( '../CFUnit/CFTestResult.php' );

class CFMock
{
    private static $mockCount = 0;
    
    public static function Create ( $instance )
    {
        self::$mockCount++;
        $mockClass = 'CFMockedObject_' . self::$mockCount;
        
        $className = get_class ( $instance );
        $reflector = new \ReflectionClass ( $className );
        $classFile = $reflector->getFileName ( );
        $interfaces = $reflector->getInterfaceNames ( );
        $methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
        
        $methodStrings = '';
        foreach ( $methods as $method )
        {
            $parameters = $method->getParameters();
            $methodStrings .= self::createMethodString($method, $parameters);
        }
        
        $content = file_get_contents ( $classFile );
        $mockContent = file_get_contents ( getcwd() . 
            DIRECTORY_SEPARATOR . '..' . 
            DIRECTORY_SEPARATOR . 'CFMock' . 
            DIRECTORY_SEPARATOR . 'CFMockedObject.php' );
            
        $implements = ( empty($interfaces) ? '' : 'implements' . implode(', ', $interfaces) );
            
        $mockContent = str_replace ( '{mock_file}', $classFile, $mockContent );
        $mockContent = str_replace ( '{mock_class}', $implements, $mockContent );
        $mockContent = str_replace ( 'CFMockedObject', $mockClass, $mockContent );
        $mockContent = preg_replace ( '~CFMockedObject(.*?){~s', '$0 '.$methodStrings, $mockContent );
        
        eval ( $mockContent );
        
        return new $mockClass ( $instance );
    }
    
    private static function createMethodString ( $method, $params )
    {
        $paramStrings = array ( );
        foreach ( $params as $param )
        {
            $paramStrings[] = '$'.$param->name;
        }
        
        $method = 'public function '.$method->name.' ( '.implode(', ', $paramStrings).' ) { return $this->__call(\''.$method->name.'\', array() ); }  ';
        return $method;
    }
}

?>