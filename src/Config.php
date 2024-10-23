<?php 
namespace Easysite\Library;
use Easysite\Library\Config\ConfigStorage;
use Easysite\Library\Exeptions\EasysiteExeption;

class Config {
    private $config;
    function __construct(){
        $this->config=new ConfigStorage;
    }
    
    public function getAll(){
        return $this->config;
    }
    public function get(string $key,$default=null){  
        $key=$this->getKeyName($key);      
        if($this->issetKey($key))
            return $this->config->$key;
        return $default; 
    }
    public function set(string $key,$value){
        $key=$this->getKeyName($key);
        $this->config->$key=$value;            
    }
    protected function issetKey(string $key){
        $key=$this->getKeyName($key);
        if(empty($key)) throw new EasysiteExeption('None key');
        
        if(isset($this->config->$key)) return true;
        return false;
    }
    protected function getKeyName(string $key){
        $key=trim(str_replace(['-','_'],':',$key));
        $keyTmp=explode(':',$key);
        $keyItems=array_map(fn($item)=>ucfirst($item),$keyTmp);
        return lcfirst(implode('',$keyItems));
    }

    protected function loadConfig(){
       $dirStorage=ROOT_PATH.'/storage/';
       if(file_exists($dirStorage.'config')){
            
       } 
    }
}