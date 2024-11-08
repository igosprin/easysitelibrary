<?php
namespace Easysite\Library\Interface\FileSystems;
interface DirectoryInterface{
    public function createDir(string $path,int $permission=0777);
    public function deleteDir(string $path);
    public function issetDir(string $path);
    public function permissionDir(string $path,$permission=0777);
}