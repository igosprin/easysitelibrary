<?php namespace Easysite\Library\Interface;

interface SqlRepositoryInterface{
    /**
     * Select row by id
     */
    public function getById(string $table,int | string $id,array $select=[]); 
    
    /**
     * Select row
     */
    public function fetchOne(string $sql,array $params=[]); 
    
    /**
     * Select all rows
     */
    public function fetchAll(string $sql,array $params=[]); 

    /**
     * Insert row
     */
    public function insert(string $table,array $params=[]);

    /**
     * Insert rows
     */
    public function insertArray(string $table,array $params=[]);
    
    /**
     * Update row 
     */
    public function update(string $table,array $where=[],array $params=[]);

    /**
     * Delete rows
     */
    public function delete(string $table,array $where=[]);
    
    /**
     * Execute query 
     */
    public function query(string $sql,array $params=[]);



}