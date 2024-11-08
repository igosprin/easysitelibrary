<?php
namespace Easysite\Library\Interface;
interface CacheInterface{
    /**
     * Save the value to cache.
     * @param string $key
     * @param mixed $value
     * @param int $lifeTime
     * @return void
     */
    public function set(string $key,mixed $value,int $lifeTime=0):void;
    /**
     * Extract the value into cache.
     * @param string $key
     * @return mixed
     */
    public function get(string $key):mixed;
    /**
     * Clear cache by its key.
     * @param string $key
     * @return void
     */
    public function clear(string $key):void;
    /**
     * Check for cache existence
     * @param string $key
     * @return bool
     */
    public function isset(string $key):bool;
    /**
     * Get value from cache or execute specified closure and store result
     * @param string $key
     * @param callable $callback
     * @param int $lifeTime
     * @return mixed
     */
    public function make(string $key,callable $callback,int $lifeTime=0);

}