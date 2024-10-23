<?php
namespace  Easysite\Library\Interface\Storage;

interface StorageSessionInterface{
    public function getLifeTime();
    public function getDriver():string;
    public function getAliase():string;
    public function getFilePath():string;
    public function getCustom();
}