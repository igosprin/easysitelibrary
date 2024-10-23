<?php
namespace Easysite\Library;
use Easysite\Library\Interface\Storage\StorageSessionInterface;
use Easysite\Library\Session\SessionStandart;
use Easysite\Library\Helpers;
use Easysite\Library\Instance;

class Session extends Instance {
    private $driverName;
    private $config;
    function __construct(StorageSessionInterface $config){
        $this->config=$config;
        $this->build($this->config->getDriver());
    }

    public function build($driver='Standart'){
        $driver=Helpers::getDriverName($driver);
        $methodName='build'.$driver;
        if(method_exists($this,$methodName)){
            $this->driverName=$methodName;
            return $this->$methodName();
        }
        else{
            return $this->getDefaultDriver();
        }
    }
    protected function buildStandart(){
        return new SessionStandart($this->config);
    }
    public function getDefaultDriver(){       
        return 'Standart';
    }

    public function getInstance(){
        return 'Standart';
    }

   
}