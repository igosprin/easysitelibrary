<?php
namespace  Easysite\Library\Interface\Config;

use Easysite\Library\Interface\Config\ConfigDbElementInterface;

interface ConfigDbInterface{
       
    /**
     * Get config aliase 
     * @return string
     */
    public function getConfigByAliase(string $aliaseName):ConfigDbElementInterface|null;
    /**
     * Get all aliase 
     * @return array
     */
    public function getAll():array;
    
}