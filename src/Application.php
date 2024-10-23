<?php
namespace Easysite\Library;

use Easysite\Library\Route;
use Easysite\Library\Session;

class Application
{
    private $route;
    private $controller_path = '';
    private $view_path = '';
    private $languages_list;
    private $config;
    private $session;
    function __construct(Config $config)
    {
        $this->config=$config;
        $this->session=new Session($config->get('session'));
        $this->route = new Route($config);
        $this->controller_path = $config->get('pathController');
        $this->view_path = $config->get('viewPath');
        $this->languages_list = $config->get('languagesList',['eng']);
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

        return new $className;

    }
    private function getLoadError(string $error)
    {
        echo $error;
        die('error');
    }
}