<?php
namespace Easysite\Library\Interface;

interface SessionInterface{
    public function get(string $key,$default=null);
    public function set(string $key,$value);
    public function remove(string $key);
    public function destroy();

}