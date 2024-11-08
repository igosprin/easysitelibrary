<?php
namespace  Easysite\Library\Interface\Config;

interface ConfigCacheInterface{
    /**
     * Get type driver
     * @return string
     */
    public function getDriver():string;
    /**
     * Get sold for cache file
     * @return string
     */
    public function getAliase():string;
    /**
     * Get path to store all caches
     * @return string
     */
    public function getPath():string;
}