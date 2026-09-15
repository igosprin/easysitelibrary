<?php
namespace Easysite\Library;

class Command
{
    public array $_params = [];
    public \Easysite\Library\Db $dbRepository;
    public $_model;

    public function output(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    public function error(string $message): void
    {
        fwrite(STDERR, $message . PHP_EOL);
    }
}
