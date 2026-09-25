<?php 
namespace Easysite\Library\Helpers;

class Helpers{
    
    static function getDriverName($driver){
        return is_null($driver) ? $driver : strtolower (trim($driver));
    }
    static function getDbConnector($connector): string{
        $connector=trim($connector);
        return strlen($connector)==0 ? '' : ucfirst(strtolower ($connector));
    }
    static function getKeyCache(string $inputKey){
        $inputKey=trim($inputKey);        
        return str_replace(':','/',$inputKey);   
    }
    static function getKeyConfig(string $inputKey):string {
        return self::camelCase($inputKey);    
    }
    static function camelCase($input):string {
        $input=trim(str_replace(self::getArraySymbols(),'',$input));
        $input=str_replace(['-','_'],':',$input); 
        return lcfirst(implode('',array_map(fn($item)=>ucfirst($item),explode(':',$input))));
    }

    static function getArraySymbols(){
        return ['"',"/","'",'\\','.','?',',',':','!','@','#','$','%','+','*','^','&','(',')','{','}','[',']'];
    }
}