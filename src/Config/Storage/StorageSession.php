<?php namespace Easysite\Library\Config\Storage;

use Easysite\Library\Interface\Storage\StorageSessionInterface;
class StorageSession implements StorageSessionInterface{
    private $lifeTime;
    private $driver;
    private $aliase;
    private $filePath;
    private $custom;

    function __construct(mixed $configSession){
        $this->setConfig($configSession);
    }

    protected function setConfig(mixed $configSession){
       foreach ($configSession as $key => $value) {
          if($this->$key)  $this->$key=$value;
          else $this->custom[$key]=$value; 
       } 
    }
    public function getLifeTime(){
        return $this->lifeTime;
    }
    public function getDriver(): string{
        return $this->driver;
    }
    public function getAliase(): string{
        return $this->aliase;
    }
    public function getFilePath():string{
        return $this->filePath;
    }
    public function getCustom(){
        return $this->custom;
    }
}
