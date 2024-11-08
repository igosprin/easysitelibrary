<?php
namespace Easysite\Library\Storage;


class CacheFileStorage extends Storage{
    public static $storageName='cache';
    public static $storageClass=\Easysite\Library\File\FileManager::class;

}