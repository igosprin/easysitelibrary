<?php
namespace Easysite\Library\Storage;

use Easysite\Library\Exeptions\EasysiteExeption;

class Storage {
    protected static $storages; 
    public static function createStorage(){        
        $storageName=static::$storageName;
        $storageClass=static::$storageClass;     
        $storagePath=static::$storagePath ?? $storageName;     
        if(!isset(static::$storages[$storageName])){
            static::$storages[$storageName]=new $storageClass($storageName); 
            return static::$storages[$storageName];
        }
    }
    public static function getStorage(){ 
        if(isset(Storage::$storages[static::$storageName])) 
            return static::$storage[static::$storageName]; 
        return null;       
    }
    public static function getStorageName(){       
            return static::$storageName;              
    }

}