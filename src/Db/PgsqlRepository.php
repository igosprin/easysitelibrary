<?php namespace Easysite\Library\Db;

use Easysite\Library\Config\ConfigDbElement;
use Easysite\Library\Db\Class\SqlRepository;

class PgsqlRepository extends SqlRepository{
    function __construct(ConfigDbElement $config){        
        $this->addRepository(\Easysite\Library\Db\Connection::connect($config),$config);               
    }
}