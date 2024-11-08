<?php 
namespace Easysite\Library;
use Easysite\Library\Exeptions\EasysiteExeption;
use Easysite\Library\Helpers\Helpers;

class Config {
    protected static $config;
    /**
     * Get value from config
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key,$default=null): mixed{  
        if(static::has($key))
            return static::$config[$key];
        return $default; 
    }
    /**
     * Show all keys and values
     * @return mixed
     */
    public static function getAll(){
        return static::$config;
    }
    /**
     * Create value in config
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key,$value){
        $key=Helpers::getKeyConfig($key);      
        static::$config[$key]=$value;                    
    }    
    /**
     * Summary of has
     * @param mixed $key
     * @throws \Easysite\Library\Exeptions\EasysiteExeption
     * @return bool
     */
    public static function has($key): bool{
        $key=Helpers::getKeyConfig($key);
        if(empty($key)) throw new EasysiteExeption('None key');
        if(!isset(static::$config[$key])) return false;
        return true;
    }

}