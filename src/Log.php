<?php
namespace Easysite\Library;

use Easysite\Library\Instance\FileManager;
use Throwable;

class Log{
    public static function log(string $message=''){
        if($message==='') return;
        self::write('DEBUG',$message);
    }
    public static function info(string $message){
        self::write('INFO',$message);
    }
    public static function error(string $message){
        self::write('ERROR',$message);
    }
    public static function exception(Throwable $e){
        self::write('ERROR',sprintf(
            '%s: %s in %s:%d'.PHP_EOL.'%s',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    }
    private static function write(string $level,string $message){
        // Log::log() is called from Instances::runInstance() while FileManager
        // is still being created (its own bootstrap) — FileManager isn`t usable yet then.
        if(FileManager::getInstance()===null) return;

        $disk=Config::get('log')['disk'] ?? null;
        if(empty($disk)) return;

        if(!FileManager::issetDir($disk)){
            FileManager::createDir($disk);
            FileManager::permissionDir($disk,0777); // mkdir() mode is umask-clipped, chmod explicitly like storage/cache
        }

        $file=$disk.'/'.date('Y-m-d').'.log';
        $line=sprintf('[%s] [%s] %s'.PHP_EOL,date('Y-m-d H:i:s'),$level,$message);
        FileManager::appendFile($file,$line);
    }
}
