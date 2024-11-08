<?php 
namespace Easysite\Library\Instance;
use \Easysite\Library\Instances;
/**
 * Cache instance
 *
 * @method static void set($key,$value,$lifeTime=0);
 * @method static mixed get($key);
 * @method static void clear($key);
 * @method static void isset($key);
 * @method static mixed make($key,$callback,$lifeTime=0);
 *
 * @see \Easysite\Library\Interface\CacheInterface
 */

class Cache extends Instances {
    protected static function getDriversAliaces(){        
        return[
            'file'=>\Easysite\Library\Cache\CacheFile::class
        ];
   }
    protected static function instanceAliace(){        
        return 'cache';
    }  
}

