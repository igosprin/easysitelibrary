<?php
namespace Easysite\Library;
use Easysite\Library\Helpers\Helpers;
use RuntimeException;

abstract class Instances{
    protected static $appInstances;
    public static $classInstance;    
    public static function runInstance($driver=null,$config=null){        
        $instanceAliace=static::instanceAliace();
        if(!self::issetInstance($instanceAliace))
        {
            if(!is_null($driver))
                $class=self::searchClass($driver);
            else
                $class=static::$classInstance;
            if(!is_null($class)){
                static::$appInstances[$instanceAliace]=is_null($config) ? new $class : new $class($config);          
            }
        }        
    }
    
    public static function getInstance(){       
        if(isset(static::$appInstances[static::instanceAliace()]))  
            return static::$appInstances[static::instanceAliace()]; 
        return null;
            
    }
    public static function getInstances(){
        return static::$appInstances;        
    }
    protected static function searchClass(string $driver){        
        $driver=Helpers::getDriverName($driver);
        $aliaces=static::getDriversAliaces();     

        if(isset($aliaces[$driver])) return $aliaces[$driver];
        return null;        
    }
    protected static function issetInstance($instance){
        if(isset(static::$appInstances[$instance])) return true;
        return false;
    }
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        if ($instance==null) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }

}