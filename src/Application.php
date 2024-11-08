<?php
namespace Easysite\Library;

use Easysite\Library\Route;
use Easysite\Library\Config;
use Easysite\Library\Instance\Session;
use Easysite\Library\Instance\Cache;
use Easysite\Library\Instance\FileManager;

class Application
{
    private $route;
    private $controller_path = '';
    private $view_path = '';
    private $languages_list;
    private $instances;
    function __construct()
    {
        Log::log('');
        $this->onRunInstances();
        Log::log('');
        $this->route = new Route(); 
        Log::log('');    
        $this->controller_path = Config::get('pathController');
        $this->view_path = Config::get('viewPath');
        $this->languages_list = Config::get('languagesList',['eng']);
        Log::log('');
    }
    protected function onRunInstances(){        
        Session::runInstance(Config::get('session')->getDriver(),Config::get('session'));
        Log::log('');
        FileManager::runInstance();
        Log::log('');
        Cache::runInstance(Config::get('cache')->getDriver(),Config::get('cache'));     
    }
    public function init()
    {
        $this->loadEvent($this->route->searchEvent());
    }

    private function loadEvent(mixed $action)
    {

        if (!isset($action['controller']))
            $this->getLoadError('None  url');

        if (!isset($action['action']))
            $this->getLoadError('None  url');

        $event_class = $this->getEventClass($action['controller']);
        if (!$event_class)
            $this->getLoadError('None class');

        $event_class->setRequest($this->languages_list);
        $event_class->setViewPath($this->view_path);
        $event_class->_url_params = $action['params'];

        $method_name = $action['action'] . 'Action';
        if (!method_exists($event_class, $action['action'] . 'Action')) {
            unset($event_class);
            $this->getLoadError('None action in class');
        }
        $event_class->$method_name();
    }


    private function getEventClass(string $className)
    {

        if (empty($className))
            return false;
        $className .= 'Controller';
        $class_path = $this->controller_path . $className . '.php';

        if (!file_exists($class_path))
            return false;
        require $class_path;

        return new $className();

    }
    private function getLoadError(string $error)
    {
        echo $error;
        die('error');
    }
}