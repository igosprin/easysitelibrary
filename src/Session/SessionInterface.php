<?php
namespace Easysite\Library\Session;

interface SessionInterface{
    public function get(string $key,$default=null);
    public function set(string $key,$value);
    public function remove(string $key);

}