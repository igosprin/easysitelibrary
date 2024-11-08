<?php namespace Easysite\Library\Config;

use Easysite\Library\Interface\Config\ConfigCacheInterface;
class ConfigCache implements ConfigCacheInterface{
    private $driver;
    private $aliase;
    private $path;

    function __construct(mixed $config){
        $this->setConfig($config);
    }

    protected function setConfig(mixed $config){        
       foreach ($config as $key => $value) {
          if(property_exists($this, $key))  $this->$key=$value;
       } 
    }
    /**
    * @inheritDoc
    */
    public function getDriver(): string{
        return $this->driver;
    }
    /**
    * @inheritDoc
    */
    public function getAliase(): string{
        return $this->aliase;
    }
    /**
    * @inheritDoc
    */
    public function getPath(): string{
        return $this->path;
    }
}
