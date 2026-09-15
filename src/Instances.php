<?php
namespace Easysite\Library;

use Easysite\Library\Exeptions\InstanceExeption;
use Easysite\Library\Helpers\Helpers;

abstract class Instances{
    protected static $appInstances;    
   

    public static function runInstance(){
               
        $instanceAliace=static::instanceAliace();
        Log::log('create instance '.$instanceAliace); 
        if(!self::issetInstance($instanceAliace))
        {
            $driver=static::getDriver();
            $config=static::getConfig();         
            
            $class=self::searchClass($instanceAliace,$driver);           
            if(!is_null($class)){
                static::$appInstances[$instanceAliace]=is_null($config) ? new $class : new $class($config); 
                                   
            }
            else{
               throw new InstanceExeption('No set instance `'.$instanceAliace.'`');
            }
        }        
    }
    
    public static function getInstance(){                    
        if(isset(static::$appInstances[static::instanceAliace()])){ 
            return static::$appInstances[static::instanceAliace()];
        }              
        return null;            
    }
    public static function getInstances(){
        return static::$appInstances;        
    }
    protected static function searchClass(string $alias, ?string $driver=null){
        $driver=Helpers::getDriverName($driver) ;
        $aliaces=self::getDriversAliaces($alias);        
        if(is_null($aliaces)) return null;         
        if(is_null($driver)){  
            return $aliaces;
        }
        if(isset($aliaces[$driver])) return $aliaces[$driver];
        return null;        
    }
    protected static function issetInstance($instance){
        if(isset(static::$appInstances[$instance])) return true;
        return false;
    }
    protected static function getDriversAliaces(string $alias){
        $aliaces=Config::get('instances');
        
        if(isset($aliaces[$alias])) return $aliaces[$alias];
        return null;
    }
    protected static function getConfig()
    {
        return null;
    }
    protected static function getDriver()
    {
        return null;
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        if ($instance==null) {
            throw new InstanceExeption('A instance root has not been set.');
        }

        return $instance->$method(...$args);
    }

}
