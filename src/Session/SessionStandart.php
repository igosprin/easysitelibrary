<?php
namespace Easysite\Library\Session;
use Easysite\Library\Interface\Config\ConfigSessionInterface;


class SessionStandart implements \Easysite\Library\Interface\SessionInterface {
    private mixed $config;
    function __construct(ConfigSessionInterface $config){
        
        session_name($config->getAliase());
        
       /* if(isset($config['life_time']))
        ini_set();*/
        $this->start();
        //var_dump($config);
    }
    function start(){
        session_start();
    }
    function get(string $key, $default = null){
        return  (empty($_SESSION[$key])) ? $default : $_SESSION[$key];
    }
    function set(string $key, $value){
        $_SESSION[$key]=$value;
    }

    function remove($key){
        unset($_SESSION[$key]);
    }
    function destroy(){
        session_destroy();
    }
    
}