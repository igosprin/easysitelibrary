<?php 
namespace Easysite\Library;

class Helpers{
    static function getDriverName($driver){
        return ucfirst (strtolower (trim($driver)));
    }
}