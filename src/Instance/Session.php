<?php
namespace Easysite\Library\Instance;

use Easysite\Library\Config;
use Easysite\Library\Instances;

/**
 * Session instance
 *
 * @method static mixed get(string $key,$default=null);
 * @method static void set(string $key,$value);
 * @method static void remove(string $key);
 * @method static void destroy();
 *
 * @see \Easysite\Library\Interface\SessionInterface
 */

class Session extends Instances {
    protected static function instanceAliace(){        
        return 'session';
    }
    protected static function getConfig()
    {
        return Config::get('session');
    }
    protected static function getDriver()
    {
       return Config::get('session')->getDriver();
    }  
}