<?php
namespace Easysite\Library\Instance;
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
    protected static function getDriversAliaces(){        
        return[
            'standart'=>\Easysite\Library\Session\SessionStandart::class
        ];
   }
    protected static function instanceAliace(){        
        return 'session';
    }  
}