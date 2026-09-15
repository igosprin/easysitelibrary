<?php namespace Easysite\Library\Config;

use Easysite\Library\Helpers\Helpers;
use Easysite\Library\Interface\Config\ConfigDbElementInterface;


class ConfigDbElement implements ConfigDbElementInterface{

    private string $connector;
    private string $host;
    private string $port='3306';
    private string $dbname;
    private string $username;
    private string $password;
    private string $charset;
    private string $aliase;
    private bool $debug;
    

    function __construct(mixed $configSession){
        $this->setConfig($configSession);
    }

    protected function setConfig(mixed $configSession){        
       foreach ($configSession as $key => $value) {
          if(property_exists($this, $key))  $this->$key=$value;
       } 
    }
    /**
     * @inheritDoc
     */
    public function getTypeConnector(): string
    {
        return Helpers::getDbConnector($this->connector); 
    }
    /**
     * @inheritDoc
     */
    public function getHost(): string{
        return $this->host;
    }
    /**
     * @inheritDoc
     */
    public function getPort(): string
    {
        return $this->port;
    }
    /**
     * @inheritDoc
     */
    public function getDbName(): string{
        return $this->dbname;
    }    
    /**
     * @inheritDoc
     */
    public function getCharset():string{
        $this->charset=trim($this->charset);
        if(strlen($this->charset)==0) $this->charset = 'UTF8';
        return $this->charset;
    }
    /**
     * @inheritDoc
     */
    public function getPassword():string{
        return $this->password;
    }
    /**
     * @inheritDoc
     */
    public function getUserName():string{
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getAliase(): string
    {
        return $this->aliase;
    }
    /**
     * @inheritDoc
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }
}
