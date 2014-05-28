<?php

/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */
namespace Mgr\Cache;

/**
 * @name XCache 
 * @description Handles XCache Caching
 * 
 */
class XCache Implements \Mgr\Cache\CacheInterface{
    
    /**
     *
     * @var integer $ttl
     * @desctiption time to live
     */
    private $ttl;


    public function __construct($ttl=0, $host=null, $port=null) {
        $this->ttl =$ttl;
    }
    
     /**
     * @name set
     * @description set value to cache
     * 
     * @param string $key cache label
     * @param string $value cache value
     * @return bool
     */
    public function set($key, $value){
        return xcache_set($key, $value, $this->ttl);
    }
    
     /**
     * @name get
     * @description get value from cache
     * 
     * @param string $key cache label
     * @return mixed
     */
    public function get($key){
        return xcache_get($key);
    }
    
    /**
     * @name delete
     * @description delete cache by label
     
     * @param string $key cache label
     * @return bool
     */
    public function delete($key){
        return xcache_unset($key);
    }
    
    
    /**
     * @name exists
     * @description exists value in cache
     * 
     * @param string $key cache label
     * @return bool
     */
    public function exists($key){
        return xcache_isset($key);
    }
}
