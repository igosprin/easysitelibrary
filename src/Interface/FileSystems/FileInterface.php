<?php
namespace Easysite\Library\Interface\FileSystems;
interface FileInterface{
    public function createFile(string $filePath,string $content);
    public function getFile(string $filePath);
    public function deleteFile(string $filePath);
    public function permissionFile(string $filePath,$permission=0777);
    public function renameFile(string $filePath,string $newName);
    public function moveFile(string $filePath,string $filePathNew);
    public function copyFile(string $filePathSource,string $fileDestination);
   
}
