<?php namespace Easysite\Library\Db\Class;

use Easysite\Library\Exeptions\DbExeption;
use PDO;
use PDOException;
use Easysite\Library\Instance\Cache;
use PDOStatement;

trait SqlQuery{
    public function getRowsTable(PDO $pdo,string $table)
    {
        try
        {            
           return Cache::make('system:db:rows_'.$table,function() use ($pdo,$table){
                $query = $pdo->prepare("DESCRIBE {$table}");
                $query->execute();
                $columns=[]; 
                foreach($query->fetchAll() as $value){                    
                    $columns[$value['Field']]=[
                        'type'=>$value['Type'],
                    ];
                } 
                            
                return  $columns;
           });
        }
        catch(PDOException $e){
            return throw new DbExeption($e->getMessage()); 
        }
    }

    public function prepareParamsExecute(array $params_value): array
    {
        $params_=[];
        foreach($params_value as $key=>$param)
        {
            $params_[":$key"]=$param;
        }
        return $params_;
    }
    public function bindParamIUD(PDOStatement $statement,array $params_value,$params_columns)
    {
        foreach($params_value as $key=>$value)
        {
            if(!isset($params_columns[$key])) return throw new DbExeption("Undefined params $key" );
            $statement->bindValue(":{$key}",$value,$params_columns[$key]['type']=='int' ? PDO::PARAM_INT : PDO::PARAM_STR );
        }        
    }
    public function bindParamSelect(PDOStatement $statement,string $key, mixed $value)
    {   
        $statement->bindValue(":{$key}",$value,is_int($value)==true ? PDO::PARAM_INT : PDO::PARAM_STR );                
    }
    public function prepareParamsWhere(array $where)
    {
        $where_sql=[];
        $where_params=[];
        foreach($where as $key=>$value){
            $tmpParam=$this->prepareParamsWhereElement($value);
            $where_sql[]="{$key} {$tmpParam['separator']} :{$key}";
            $where_params[$key]=$tmpParam['param'];
        }
        return [
            'where_sql'=>$where_sql,
            'params'=>$where_params
        ];
    }
    protected function prepareParamsWhereElement(mixed $whereElement)
    {        
        $separator='=';
        $param=$whereElement;
        if(is_array($whereElement)){
            $separator=$whereElement[0];
            $param=$whereElement[1];
        }
        return [
            'separator'=>$separator,
            'param'=>$param
        ];
    }

    public function bindParamsSelect(PDOStatement $statement,array $params_value)
    {
        foreach($params_value as $key => $value)
        {
           $this->bindParamSelect($statement,$key,$value);           
        }        
    }
    public function preparaSelectIn(string $sql,array $params_value)
    {
        $inSelect = ['sql' => $sql,'paramsIn' => [],'params' => $params_value];
        foreach($params_value as $key=>$value){
            if(is_array($value)){
                $placeholder = str_repeat('?,', count($value) - 1) . '?';
                $inSelect['sql'] = str_replace(':' . $key,$placeholder,$inSelect['sql']);
                $inSelect['paramsIn'] = array_merge($inSelect['paramsIn'],$value);
                unset($inSelect['params'][$key]);
            }
        }
        
        return $inSelect;
    }

}