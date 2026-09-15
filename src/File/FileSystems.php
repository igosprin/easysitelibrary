<?php
namespace Easysite\Library\File;

use \Easysite\Library\Interface\FileSystems\FileInterface; 
use \Easysite\Library\Interface\FileSystems\DirectoryInterface;

class FileSystems implements FileInterface,DirectoryInterface {
    /**
     * @inheritDoc
     */    
    public function createFile(string $filePath,string $content){
       file_put_contents($filePath,$content);
    }
    /**
     * @inheritDoc
     */
    public function appendFile(string $filePath,string $content){
       file_put_contents($filePath,$content,FILE_APPEND|LOCK_EX);
    }
    /**
     * @inheritDoc
     */
    public function getFile(string $filePath): bool|string{
        if(is_readable($filePath)) 
            return file_get_contents($filePath);
        return false;
    }
    /**
     * @inheritDoc
     */
    public function deleteFile(string $filePath): bool{
        if(is_readable($filePath)) 
            return unlink($filePath);
        return false;
    }
    /**
     * @inheritDoc
     */
    public function permissionFile(string $filePath,$permission=0777){
        if(file_exists($filePath)){
            chmod($filePath,$permission);
        } 
    }
    /**
     * @inheritDoc
     */
    public function renameFile(string $filePath,string $newName){}
    /**
     * @inheritDoc
     */
    public function moveFile(string $filePath,string $filePathNew){}
    /**
     * @inheritDoc
     */
    public function copyFile(string $filePathSource,string $fileDestination){}
    /**
     * @inheritDoc
     */
    public function createDir(string $path,int $permission=0777){
        mkdir($path, $permission);
    }
    /**
     * @inheritDoc
     */
    public function deleteDir(string $path){
        if($this->issetDir($path)) rmdir($path);
    }
    /**
     * @inheritDoc
     */
    public function issetDir(string $path){
        if(file_exists($path) and is_dir($path)) 
            return true;
        return false;
    }
    /**
     * @inheritDoc
     */
    public function permissionDir(string $path,$permission=0777){
        if(file_exists($path)){
            chmod($path,$permission);
        }
    }
    
}