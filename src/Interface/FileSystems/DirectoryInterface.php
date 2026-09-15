<?php
namespace Easysite\Library\Interface\FileSystems;
interface DirectoryInterface{
    /**
     * Create dir
     * @param string $path
     * @param int $permission
     * @return void
     */
    public function createDir(string $path,int $permission=0777);
    /**
     * Delete dir
     * @param string $path
     * @return void
     */
    public function deleteDir(string $path);
    /**
     * Сheck directory existence
     * @param string $path
     * @return void
     */
    public function issetDir(string $path);
    /**
     * Change permission of dir
     * @param string $path
     * @param mixed $permission
     * @return void
     */
    public function permissionDir(string $path,$permission=0777);
}