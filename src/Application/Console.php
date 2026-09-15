<?php
namespace Easysite\Library\Application;

use Easysite\Library\ConsoleRoute;
use Easysite\Library\Config;
use Easysite\Library\Db;
use Easysite\Library\Log;

class Console extends App
{
    private ConsoleRoute $route;
    private Db $db;
    private string $command_path = '';

    function __construct(array $arrayInstances, array $argv = [])
    {
        $this->initInstances($arrayInstances);
        $this->route = new ConsoleRoute($argv ?: ($_SERVER['argv'] ?? []));
        $this->db = new Db(Config::get('database'));
        $this->command_path = Config::get('pathCommand');
    }

    public function init()
    {
        $this->loadEvent($this->route->searchEvent());
    }

    private function loadEvent(array $action)
    {
        $event_class = $this->getEventClass($action['controller']);
        if (!$event_class)
            $this->getLoadError('Command not found: ' . $action['controller']);

        $event_class->_params = $action['params'];
        $event_class->dbRepository = $this->db;
        $event_class->_model = $this->getEventModel($action['controller'], $this->db);

        $method_name = $action['action'] . 'Action';
        if (!method_exists($event_class, $method_name)) {
            unset($event_class);
            $this->getLoadError('Action not found: ' . $action['action'] . ' in ' . $action['controller'] . 'Command');
        }
        $event_class->$method_name();
    }

    private function getEventClass(string $className)
    {
        if (empty($className))
            return false;
        $className .= 'Command';
        $class_path = $this->command_path . $className . '.php';

        if (!file_exists($class_path))
            return false;
        require $class_path;

        return new $className();
    }

    private function getEventModel(string $modelName, $dbRepository)
    {
        if (!empty($modelName)) {
            $modelName .= 'Model';
            $model_path = Config::get('pathModel') . $modelName . '.php';
            if (file_exists($model_path)) {
                require $model_path;
                return new $modelName($dbRepository);
            }
        }
        return null;
    }

    private function getLoadError(string $error)
    {
        Log::error($error);
        fwrite(STDERR, $error . PHP_EOL);
        exit(1);
    }
}
