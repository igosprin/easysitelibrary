<?php
namespace Easysite\Library;
class Log{
    public static function log(string $message){
        echo '<b>'.memory_get_peak_usage().'</b><br>';
    }
}