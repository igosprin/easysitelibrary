<?php 
namespace Easysite\Library\Application;
use Easysite\Library\Route;
use Easysite\Library\Config;
use Easysite\Library\Db;

class Http extends App
{
    private $route;
    private $db;
    private $controller_path = '';
    private $view_path = '';
    private $languages_list;
    function __construct(array $arrayInstances)
    {
        $this->initInstances($arrayInstances);       
        $this->route = new Route();
        $this->db = new Db(Config::get('database'));         
        $this->controller_path = Config::get('pathController');
        $this->view_path = Config::get('viewPath');
        $this->languages_list = Config::get('languagesList',['eng']);
        
    }
   
    /*protected function onRunInstances(){
        \Easysite\Library\Instance\FileManager::runInstance();     
        Session::runInstance(Config::get('session')->getDriver(),Config::get('session'));
        Log::log('');        
        Log::log('');
        Cache::runInstance(Config::get('cache')->getDriver(),Config::get('cache'));     
    }*/
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

        // 'dir' — optional subdirectory of application/controllers/ from the route config
        $event_class = $this->getEventClass($action['controller'], $action['dir'] ?? '');
        if (!$event_class)
            $this->getLoadError('None class');

        $event_class->setRequest($this->languages_list);
        $event_class->setViewPath($this->view_path);
        $event_class->_url_params = $action['params'];
        $event_class->dbRepository = $this->db;
        $event_class->_model = $this->getEventModel($action['controller'],$this->db);

        $method_name = $action['action'] . 'Action';
        if (!method_exists($event_class, $action['action'] . 'Action')) {
            unset($event_class);
            $this->getLoadError('None action in class');
        }

        if (!$this->runMiddleware($event_class, $action))
            return;

        $event_class->$method_name();
    }

    /**
     * First the middleware from config/middleware.php['global'] (with $params = null), then
     * the ones the route lists in 'middleware' => [name => params]. The same middleware runs
     * once — at the position of its first mention, but with the route's params.
     * false from handle() means the request is finished (redirect/denial), the action is not called.
     */
    private function runMiddleware(object $controller, array $action): bool
    {
        $config = Config::get('middleware', []);
        $aliases = $config['aliases'] ?? [];

        $pipeline = array_fill_keys($config['global'] ?? [], null);
        foreach ($action['middleware'] ?? [] as $name => $params)
            $pipeline[$name] = $params;

        foreach ($pipeline as $name => $params) {
            if (!isset($aliases[$name]))
                throw new \InvalidArgumentException("Unknown middleware: {$name}");

            $middleware = new $aliases[$name]();
            $middleware->dbRepository = $this->db;
            if ($middleware->handle($controller, $params) === false)
                return false;
        }

        return true;
    }

    private function getEventClass(string $className, string $area = '')
    {
        if (empty($className))
            return false;
        $className .= 'Controller';
        $dir = $area !== '' ? $area . '/' : '';
        $class_path = $this->controller_path . $dir . $className . '.php';

        if (!file_exists($class_path))
            return false;
        require $class_path;

        return new $className();
    }
    private function getEventModel(string $modelName, $dbRepository)
    {
        if (!empty($modelName)){
            $modelName .= 'Model';
            $model_path = Config::get('pathModel') . $modelName . '.php'; 
            if (file_exists($model_path)){
                require $model_path;
                return new $modelName($dbRepository);
            }
        }
        return null;
    }
    private function getLoadError(string $error)
    {
        // getLoadError must always terminate the request — loadEvent() keeps running
        // past its call sites and would use a bogus $event_class otherwise.
        $event_class = $this->getEventClass('error');
        if ($event_class && method_exists($event_class, 'error404')) {
            $event_class->setRequest($this->languages_list);
            $event_class->setViewPath($this->view_path);
            $event_class->error404($error);
            exit;
        }
        echo $error;
        die('error');
    }
}