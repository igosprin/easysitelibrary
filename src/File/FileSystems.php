<?php
namespace Easysite\Library\File;

use \Easysite\Library\Interface\FileSystems\FileInterface; 
use \Easysite\Library\Interface\FileSystems\DirectoryInterface;

class FileSystems implements FileInterface,DirectoryInterface {
        
    public function createFile(string $filePath,string $content){
       file_put_contents($filePath,$content);
    }
    public function getFile(string $filePath): bool|string{
        if(is_readable($filePath)) 
            return file_get_contents($filePath);
        return false;
    }
    public function deleteFile(string $filePath): bool{
        if(is_readable($filePath)) 
            return unlink($filePath);
        return false;
    }
    public function permissionFile(string $filePath,$permission=0777){
        if(file_exists($filePath)){
            chmod($filePath,$permission);
        } 
    }
    public function renameFile(string $filePath,string $newName){}
    public function moveFile(string $filePath,string $filePathNew){}
    public function copyFile(string $filePathSource,string $fileDestination){}
    public function createDir(string $path,int $permission=0777){
        mkdir($path, $permission);
    }
    public function deleteDir(string $path){
        if($this->issetDir($path)) rmdir($path);
    }
    public function issetDir(string $path){
        if(file_exists($path) and is_dir($path)) 
            return true;
        return false;
    }
    public function permissionDir(string $path,$permission=0777){
        if(file_exists($path)){
            chmod($path,$permission);
        }
    }
    
}