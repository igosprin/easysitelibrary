<?php
namespace Easysite\Library\Interface\FileSystems;
interface FileInterface{
    /**
     * Create file
     * @param string $filePath
     * @param string $content
     * @return void
     */
    public function createFile(string $filePath,string $content);
    /**
     * Append content to file, creating it if it doesn`t exist yet
     * @param string $filePath
     * @param string $content
     * @return void
     */
    public function appendFile(string $filePath,string $content);
    /**
     * Read file into string
     * @param string $filePath
     * @return void
     */
    public function getFile(string $filePath);
    /**
     * Delete file
     * @param string $filePath
     * @return void
     */
    public function deleteFile(string $filePath);
    /**
     * Change permission of file
     * @param string $filePath
     * @param mixed $permission
     * @return void
     */
    public function permissionFile(string $filePath,$permission=0777);
    /**
     * Rename file
     * @param string $filePath
     * @param string $newName
     * @return void
     */
    public function renameFile(string $filePath,string $newName);
    /**
     * Move file
     * @param string $filePath
     * @param string $filePathNew
     * @return void
     */
    public function moveFile(string $filePath,string $filePathNew);
    /**
     * Copy file
     * @param string $filePathSource
     * @param string $fileDestination
     * @return void
     */
    public function copyFile(string $filePathSource,string $fileDestination);
   
}
