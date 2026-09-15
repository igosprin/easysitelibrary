<?php namespace Easysite\Library\Db;

use Easysite\Library\Config\ConfigDbElement;
use Easysite\Library\Exeptions\DbExeption;
use PDO;
use PDOException;

class Connection{    
    static function connect(ConfigDbElement $config): PDO
    {        
        try {
            $pdo = new PDO(self::getDsn($config), $config->getUserName(), $config->getPassword(),[
                PDO::ATTR_EMULATE_PREPARES => false, 
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
              ]);

            if ($pdo) {                
                return $pdo;
            }
            
        } catch (PDOException $e) {
            return throw new DbExeption($e->getMessage());            
        }
    }

    static function getDsn(ConfigDbElement $config):string
    {
        $connecorType=strtolower($config->getTypeConnector());
        $host=$config->getHost();
        $dbName=$config->getDbName();
        $charset = $config->getCharset();
        $port = $config->getPort();
        return "$connecorType:host=$host;port=$port;dbname=$dbName;charset=$charset";
    }
}