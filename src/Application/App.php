<?php namespace Easysite\Library\Application;
abstract class App{
    public function initInstances($arrayInstances=[]){        
        
        if(count($arrayInstances)>0){
            foreach($arrayInstances as $instance){
                $instance::runInstance();
            } 
        }        
    }
    abstract public function init();

} 