<?php
namespace Easysite\Library;

abstract class Instance{   

    abstract function build($driver);

    abstract function getDefaultDriver();

    abstract function getInstance();
}