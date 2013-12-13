<?php
require_once ( 'CFFixture.php' );
require_once ( '../CFUnit/CFTestResult.php' );

class CFMock
{
    private static $mockCount = 0;
    
    public static function Create ( $className )
    {
        self::$mockCount++;
        $mockClass = 'CFMockedObject_' . self::$mockCount;
        
        $reflector = new \ReflectionClass ( $className );
        $classFile = $reflector->getFileName ( );
        $interfaces = $reflector->getInterfaceNames ( );
        $methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
        
        $constructParams = array ( );        
        if ( $reflector->hasMethod('__construct') )
        {
            $constructParamCount = $reflector->getMethod('__construct')->getParameters();
            foreach ( $constructParamCount as $param )
            {
                $paramValue = null;
                if($param->isArray())
                {
                    $type = 'array';
                }
                else
                {
                    $type = ($param->getClass() ? $param->getClass()->name : '' );
                    if ( $type == 'stdClass' )
                        $paramValue = new stdClass();
                    else
                        $paramValue = null;
                }
                
                $paramStrings[] = $type . ' $'.$param->name;
                
                $constructParams[] = $paramValue;
            }
        }
        
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
            
        $implements = ( empty($interfaces) ? '' : 'implements ' . implode(', ', $interfaces) );
            
        $mockContent = str_replace ( '{mock_file}', $classFile, $mockContent );
        $mockContent = str_replace ( '{mock_class}', $implements, $mockContent );
        $mockContent = str_replace ( 'CFMockedObject', $mockClass, $mockContent );
        $mockContent = preg_replace ( '~CFMockedObject(.*?){~s', '$0 '.$methodStrings, $mockContent );
        
        eval ( $mockContent );
        
        $reflectionMock = new \ReflectionClass($mockClass); 
        $myClassInstance = $reflectionMock->newInstanceArgs($constructParams); 
        
        return $myClassInstance;
    }
    
    private static function createMethodString ( $method, $params )
    {        
        $paramStrings = array ( );
        foreach ( $params as $param )
        {
            if($param->isArray())
            {
                $type = 'array';
            }
            else
            {
                $type = ($param->getClass() ? $param->getClass()->name : '' );
            }
            
            $paramStrings[] = $type . ' $'.$param->name;
        }
        
        $method = 'public function '.$method->name.' ( '.implode(', ', $paramStrings).' ) { return $this->__call(\''.$method->name.'\', array() ); }  ';
        return $method;
    }
}

?>