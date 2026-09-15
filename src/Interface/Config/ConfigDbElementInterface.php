<?php
namespace  Easysite\Library\Interface\Config;

interface ConfigDbElementInterface{
    /**
     * Type db
     * @return void
     */
    public function getTypeConnector();
    /**
     * Get host name
     * @return string
     */
    public function getHost():string;
    /**
     * Get database name
     * @return string
     */
    public function getDbName():string;
    /**
     * Get user name for connection 
     * @return string
     */
    public function getUserName():string;
    /**
     * Get password for connection 
     * @return string
     */
    public function getPassword():string;
    
    /**
     * Get charset 
     * @return string
     */
    public function getCharset():string;
    
    /**
     * Get aliase of connection
     * @return string
     */
    public function getAliase():string;
    
    /**
     * Get debug of connection
     * @return string
     */
    public function getDebug():bool;
    
    /**
     * Get port of connection
     * @return string
     */
    public function getPort():string;    
    
}