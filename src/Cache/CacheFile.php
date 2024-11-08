<?php
namespace Easysite\Library\Cache;


use Easysite\Library\Helpers\Helpers;
use Easysite\Library\Instance\FileManager;
use Easysite\Library\Interface\Config\ConfigCacheInterface;

class CacheFile implements \Easysite\Library\Interface\CacheInterface{
    private string $aliace;     
    private string $rootPath; 
    

    function __construct(ConfigCacheInterface $config){        
        $this->aliace=$config->getAliase();
        $this->rootPath=$config->getPath();
        //$this->global='_global';
        $this->init();
    }

    protected function init(){
        if(!FileManager::issetDir($this->rootPath)){
            FileManager::createDir($this->rootPath);
            $this->createIgnoredFile($this->rootPath);            
        }
    }
    /**
     * @inheritDoc
     */
    public function set($key,$value,$lifeTime=20): void{
       $this->setCacheFile($key,$value,$lifeTime);
    }
    /**
     * @inheritDoc
     */
    public function get($key): mixed{
        return $this->getCacheFile($key);
    }
    /**
     * @inheritDoc
     */
    public function clear($key): void{

    }
    /**
     * @inheritDoc
     */
    public function isset($key): bool{        
        return $this->issetCacheFile($this->getFileDir(Helpers::getKeyCache($key)));
    }
    /**
     * @inheritDoc
     */
    public function make($key, $callback, $lifeTime = 0): mixed{
        $value=$this->getCacheFile($key);
        if(is_null($value)){
            $value = $callback();
            $this->setCacheFile($key, $value, $lifeTime);
        }
        return $value;
    }
    protected function setCacheFile($key,$value,$lifeTime){
      
       $pathInfo=$this->setFileDir(Helpers::getKeyCache($key));
       $cacheFileSettings=$this->cacheFileSettings($pathInfo['filename']);
       
       FileManager::createFile($pathInfo['dirname'].'/'.$cacheFileSettings['cacheFile'],serialize($value));
       $contentSettings=['file_'=>$cacheFileSettings['cacheFile'],'created_at'=>time()];
       if($lifeTime>0)
            $contentSettings['expires']=time()+$lifeTime;
       FileManager::createFile($pathInfo['dirname'].'/'.$cacheFileSettings['settingsFile'],serialize($contentSettings));
    }
    protected function getCacheFile($key): mixed{      
       $pathInfo=$this->getFileDir(Helpers::getKeyCache($key));
       if(!$this->issetCacheFile($pathInfo))
            return null;
       $cacheFileSettings=$this->cacheFileSettings($pathInfo['filename']);
       $fileSettings=$this->getCacheSettingsFile($pathInfo['dirname'].'/'.$cacheFileSettings['settingsFile']);
       if($fileSettings){
            $content=FileManager::getFile($pathInfo['dirname'].'/'.$cacheFileSettings['cacheFile']);
            if(!$content) return null;
            return unserialize($content);
       }
       return null;       
    }
    protected function issetCacheFile($pathInfo): bool{       
       $cacheFileSettings=$this->cacheFileSettings($pathInfo['filename']);
       $fileSettings=FileManager::getFile($pathInfo['dirname'].'/'.$cacheFileSettings['settingsFile']);
       if($fileSettings){
            if(!file_exists(filename: $pathInfo['dirname'].'/'.$cacheFileSettings['cacheFile'])) {
                return false;
            };
            return true;
       }
       return false;       
    }

    protected function cacheFileSettings($fileName): array{
        $fileName=md5($this->aliace.'_'.$fileName);
        return [
            'cacheFile'=>$fileName,
            'settingsFile'=>'sts_'.$fileName
        ];
    }

    protected function setFileDir($key){
        $pathInfo=pathinfo($key);
        $tmp=explode('/',$pathInfo['dirname']);        
        $count=count($tmp);
        $Path=$this->rootPath;
        if($count>=1){           
            for($i=0;$i<$count;$i++){
                $Path.='/'.$tmp[$i];
                if(!FileManager::issetDir($Path)){
                    FileManager::createDir($Path);
                    $this->createIgnoredFile($Path);
                }                
            }
            $pathInfo['dirname']=$Path; 
        }
        else $pathInfo['dirname']=$Path.'/'.$pathInfo['dirname']; 
               
        return $pathInfo;
    }
    protected function getFileDir($key){
        $pathInfo=pathinfo($key);
        $pathInfo['dirname']=$this->rootPath.'/'. $pathInfo['dirname']; 
        return $pathInfo;
    }

    protected function getCacheSettingsFile($filePath): mixed{
        $fileSettings=unserialize(FileManager::getFile( $filePath));
        if($fileSettings){            
            if(isset($fileSettings['expires'])){
                if($fileSettings['expires']>=time()){
                    return true;
                }                    
                else{
                    $pathInfo=pathinfo($filePath);
                    FileManager::deleteFile($pathInfo['dirname'].'/'.$fileSettings['file_']);
                    FileManager::deleteFile($filePath);
                    return false;
                }                
            }
            else {
                return true;
            };             
        }
        return false;     
    }
    protected function getIgnoredFileContent(): string{
        return "* \n\r
!.gitignore";     
    }

    protected function createIgnoredFile($path){
        if(!file_exists($path.'/.gitignore'))
            FileManager::createFile($path.'/.gitignore',$this->getIgnoredFileContent());
    }   
}