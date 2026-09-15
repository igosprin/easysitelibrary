<?php namespace Easysite\Library;

use Easysite\Library\Config\ConfigDb;
use Easysite\Library\Interface\SqlRepositoryInterface;

class Db {
    protected static $repository;

    function __construct(ConfigDb $config){
        $this->setRepository($config);
    }

    protected function setRepository(ConfigDb $config){             
        foreach($config->getAll() as $value){            
            if($value->getTypeConnector()!=''){
                $class= $this->classConnector($value->getTypeConnector());
                if($value->getHost() == '') 
                    static::$repository[$value->getAliase()]=null;
                elseif(!is_null($class)){
                    static::$repository[$value->getAliase()]=new $class($value); 
                } 
            }
            else{
                static::$repository[$value->getAliase()]=null;
            }     
        }
    }

    protected function classConnector(string $connector){
        $defaultConnector=[
            'Mysql'=>\Easysite\Library\Db\MysqlRepository::class,
            'Pgsql'=>\Easysite\Library\Db\PgsqlRepository::class
        ];
        if(isset($defaultConnector[$connector])) return $defaultConnector[$connector];
        return null;
    }

    public function getRepositories(){
        return static::$repository;
    }
    public function getRepository(string $aliase): SqlRepositoryInterface|null{
        if(isset(static::$repository[$aliase]))
            return static::$repository[$aliase];
        return null;
    }

}