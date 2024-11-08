<?php
namespace Easysite\Library\Interface;

interface InstanceInterface{   

    public function runInstance($aliace,$driver,$config);

    public function getInstance(string $instance);
}