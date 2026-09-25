<?php namespace Easysite\Library\Db\Class;

use Easysite\Library\Config\ConfigDbElement;

use Easysite\Library\Interface\SqlRepositoryInterface;
use Easysite\Library\Exeptions\DbExeption;
use PDO;
use PDOException;

class SqlRepository implements SqlRepositoryInterface{
    use \Easysite\Library\Db\Class\SqlQuery;
    protected $pdo;
    protected ConfigDbElement $config;
    protected function addRepository(PDO $pdo,ConfigDbElement $config){               
        $this->pdo = $pdo;
        $this->config = $config;
    }
    protected function getRepository(){
        return $this->pdo;
    }

    protected function getConfig(){
        return $this->config;
    }

    private static string $debugBuffer = '';
    private static bool $debugShutdownRegistered = false;

    /**
     * debugDumpParams() echoes straight into the response body — if that happens before
     * header()/setcookie() in a controller (and ConfigDb debug=true dumps on EVERY query),
     * those silently stop working ("headers already sent").
     * So the dumps are buffered and printed all at once at the end of the script, after
     * the controller has already sent its headers/cookies/redirects.
     */
    private function debugDump($statement): void{
        ob_start();
        $statement->debugDumpParams();
        self::$debugBuffer .= ob_get_clean();

        if(!self::$debugShutdownRegistered){
            self::$debugShutdownRegistered = true;
            register_shutdown_function(function(){
                echo self::$debugBuffer;
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function getById(string $table,int | string $id, array $select = [])
    {
        $sql = "SELECT ".count($select) > 0 ? join(',',$select) : "*" ." FROM ". $table ." WHERE id = :id LIMIT 1"; 
        return $this->fetchOne($sql,['id' => $id]);
    }

    /**
     * @inheritDoc
     */
    public function fetchOne(string $sql,array $params = [])
    {
        $statement = $this->query($sql,$params);
        return  $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(string $sql,array $params = [])
    {
        $statement = $this->query($sql,$params);
        return  $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table,array $params = [])
    {
        $columns = $this->getRowsTable($this->pdo,$table);
        unset($columns['id']);        
        try{
            $columns_values = array_map(function($value){
                return ":$value";
            },array_keys($columns));
    
            $sql = 'INSERT INTO '.$table.'('.join(',',array_keys($columns)).') VALUES('.join(',',$columns_values).')';
            $statement = $this->pdo->prepare($sql); 
            $this->bindParamIUD($statement,$params,$columns);
            
            $statement->execute();
            if($this->config->getDebug()){
                $this->debugDump($statement);
            }
            unset($statement);
            return $this->pdo->lastInsertId();
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }
    }

    /**
     * @inheritDoc
     */
    public function insertArray(string $table, array $params = [])
    {

        $columns = $this->getRowsTable($this->pdo,$table);
        unset($columns['id']);        
        try{
            $columns_values = array_map(function($value){
                return ":$value";
            },array_keys($columns));
    
            $sql = 'INSERT INTO '.$table.'('.join(',',array_keys($columns)).') VALUES('.join(',',$columns_values).')';
            $statement = $this->pdo->prepare($sql); 
            foreach ($params as $param) {
                $executeStatements = [];
                foreach($param as $key =>$value){
                    $executeStatements[":{$key}"] = $value;
                }
                $statement->execute($executeStatements);
            }
            if($this->config->getDebug()){
                $this->debugDump($statement);
            }
            unset($statement);
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }
    }
       
     
    /**
     * @inheritDoc
     */
    public function update(string $table,array $where = [],array $params = [])
    {

        $columns = $this->getRowsTable( $this->pdo,$table);
        if(count($params) == 0) throw new DbExeption('Params cannot be empty');
        try{
            $where_sql = $this->prepareParamsWhere($where);
            
            $columns_set = array_map(function($value){               
                    return "{$value} = :{$value}";                                   
            },array_filter(array_keys($params),function($val) use ($where_sql) {
                return !isset($where_sql['params'][$val]);
            }));
            
            $sql = 'UPDATE '.$table.' SET '.join(',',$columns_set);
            if(count($where_sql['where_sql'])>0) $sql.= " WHERE ".join(' and ',$where_sql['where_sql']);
            
            $statement = $this->pdo->prepare($sql);
            $this->bindParamIUD($statement,$params,$columns);
            $this->bindParamIUD($statement,$where_sql['params'],$columns);
            $statement->execute();
            if($this->config->getDebug()){
                $this->debugDump($statement);
            }
            unset($statement);
            return true;
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }       
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table,array $where = [])
    {
        $columns = $this->getRowsTable( $this->pdo,$table);
        if(count($where) == 0) throw new DbExeption('Conditional cannot be empty');
        try{
            $where_sql = $this->prepareParamsWhere($where);
            $sql = 'DELETE FROM '.$table;
            if(count($where_sql['where_sql'])>0) $sql.= " WHERE ".join(' and ',$where_sql['where_sql']);
            $statement = $this->pdo->prepare($sql);
            $this->bindParamIUD($statement,$where_sql['params'],$columns);
            $statement->execute();
            if($this->config->getDebug()){
                $this->debugDump($statement);
            }
            unset($statement);
            return true;
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }
    }

   /**
     * @inheritDoc
     */
    public function query(string $sql,array $params = [])
    {
        try{
            $prepareSelectIn = $this->preparaSelectIn($sql,$params);
            $statement = $this->pdo->prepare($prepareSelectIn['sql']);

            if(count($prepareSelectIn['params'])>0){
                $this->bindParamsSelect($statement,$prepareSelectIn['params']);
            }
            
            $statement->execute(count($prepareSelectIn['paramsIn']) > 0 ? $prepareSelectIn['paramsIn'] : null);
            if($this->config->getDebug()){
                $this->debugDump($statement);
            }

            return $statement;
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }
    }
}