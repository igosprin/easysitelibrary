<?php 
namespace Easysite\Library\Instance;
use \Easysite\Library\Instances;
/**
 * Session instance
 *
 * @method static void createFile(string $filePath,string $content);
 * @method static string getFile(string $filePath);
 * @method static void deleteFile(string $filePath);
 * @method static void permissionFile(string $filePath,$permission=0777);
 * @method static void renameFile(string $filePath,string $newName);
 * @method static void moveFile(string $filePath,string $filePathNew);
 * @method static void copyFile(string $filePathSource,string $fileDestination);
 * @method static void createDir(string $path,int $permission=0777);
 * @method static void deleteDir(string $path);
 * @method static bool issetDir(string $path);
 * @method static void permissionDir(string $path,$permission=0777);
 *
 * @see \Easysite\Library\File\FileSystems;
 */

class FileManager extends Instances {
    public static $classInstance=\Easysite\Library\File\FileSystems::class;
    protected static function instanceAliace(){        
        return 'fileManager';
    }  
}

