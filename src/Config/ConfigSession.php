<?php namespace Easysite\Library\Config;

use Easysite\Library\Interface\Config\ConfigSessionInterface;


class ConfigSession implements ConfigSessionInterface{
    private $lifeTime;
    private $driver;
    private $aliase;
    private $filePath;

    function __construct(mixed $configSession){
        $this->setConfig($configSession);
    }

    protected function setConfig(mixed $configSession){        
       foreach ($configSession as $key => $value) {
          if(property_exists($this, $key))  $this->$key=$value;
       } 
    }
    /**
     * @inheritDoc
     */
    public function getLifeTime(){
        return $this->lifeTime;
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
    public function getFilePath():string{
        return $this->filePath;
    }
}
