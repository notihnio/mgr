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
 * @name Cache 
 * @description Handles Caching
 * 
 */
class Cache implements \Mgr\Cache\CacheInterface{
    
    /**
     * @var string type
     * @description the cache type;
     */
    public $type;
    
    /**
     * @var string $tti
     * @description the cache time to live;
     */
    private $ttl;
    
    /**
     * @var object cache
     * @description the specified cache instance
     */
    private $cache;


    
    public function __construct($type, $ttl, $host=null, $port=null) {
        if(!in_array($type, array("XCache", "Apc", "Memcache")))
            throw new \Mgr\Exception\Cache("The specified cache {$type} does not supported! The suported cache types are: Apc, Mecache and File");
        $cacheClass= "\\Mgr\\Cache\\{$type}";   
        $this->ttl = $ttl;
        $this->cache = new $cacheClass($this->ttl, $host, $port);        
    }
    
    /**
     * @name get
     * @description get value from cache
     * 
     * @param string $key cache label
     * @return mixed
     */
    public function get($key){
        return $this->cache->get($key);
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
        return $this->cache->set($key,$value);
    }
    
    
    /**
     * @name delete
     * @description delete cache by label
     
     * @param string $key cache label
     * @return bool
     */
    public function delete($key){
        return $this->cache->delete($key);
    }
    
    
    /**
     * @name exists
     * @description exists value in cache
     * 
     * @param string $key cache label
     * @return bool
     */
    public function exists($key){
        return $this->cache->exists($key);
    }
       
}
