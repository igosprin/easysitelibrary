<?php namespace Easysite\Library\Config;

use Easysite\Library\Interface\Config\ConfigDbElementInterface;
use Easysite\Library\Interface\Config\ConfigDbInterface;
use Easysite\Library\Config\ConfigDbElement;
use Easysite\Library\Helpers\Helpers;

class ConfigDb implements ConfigDbInterface{

    protected static $config;
    

    function __construct(mixed $config){
        $this->setConfig($config);
    }

    protected function setConfig(mixed $config){ 
       $count= count($config);
       if($count>0)
       {
            foreach ($config as $key => $value) {
                $value['aliase']= $this->getAliase($value['aliase'] ?? '',$key,$count);       
                self::$config[$value['aliase']]=new ConfigDbElement($value);                
           } 
       }       
       
    }

    protected function getAliase(string $aliace,$key,$count=1){
        $aliace=Helpers::getKeyConfig($aliace);
        if(strlen($aliace)==0){
            return $count==1 ? 'dbAliase' : 'dbAliace'.intval($key)+1 ;
        } 
        return $aliace; 
    } 

    /**
     * @inheritDoc
     */
    public function getConfigByAliase($aliaseName):ConfigDbElementInterface|null
    {
        if(isset(self::$config[$aliaseName]))
            return self::$config[$aliaseName]; 
        return null;
    }
    /**
     * @inheritDoc
     */
    public function getAll():array
    {
       return self::$config;       
    }
    
}
