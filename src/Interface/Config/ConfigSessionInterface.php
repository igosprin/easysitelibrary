<?php
namespace  Easysite\Library\Interface\Config;

interface ConfigSessionInterface{
    /**
     * Session lifetime
     * @return void
     */
    public function getLifeTime();
    /**
     * Get type store session
     * @return string
     */
    public function getDriver():string;
    /**
     * Get prefix for session name
     * @return string
     */
    public function getAliase():string;
    /**
     * Path store session file
     * @return string
     */
    public function getFilePath():string;
    
}