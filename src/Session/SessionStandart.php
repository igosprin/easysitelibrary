<?php
namespace Easysite\Library\Session;
use Easysite\Library\Interface\Config\ConfigSessionInterface;


class SessionStandart implements \Easysite\Library\Interface\SessionInterface {
    private mixed $config;

    function __construct(ConfigSessionInterface $config){
        $this->config = $config;
        $this->start();
    }

    function start(){
        // Avoid "headers already sent" if something echoed before session init.
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }
        if (headers_sent()) {
            // Cannot safely start session once headers are out; fail softly.
            return;
        }
        $alias = $this->config->getAliase();
        if (!empty($alias)) {
            session_name($alias);
        }
        // cookie недоступна из JS, не уходит с запросами с чужих сайтов (кроме переходов по ссылке),
        // по https — только по https; чужой id сессии из cookie не принимается (strict mode)
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
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
