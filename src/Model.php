<?php namespace Easysite\Library;

class Model 
{
    protected \Easysite\Library\Db $dbRepository;  
    function __construct(\Easysite\Library\Db $dbRepository)
    {
        $this->dbRepository = $dbRepository;
    }   

}