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
 * @name Memcache 
 * @description Handles Memcache Caching
 * 
 */
class Memcache Implements \Mgr\Cache\CacheInterface {

    /**
     *
     * @var integer $ttl
     * @desctiption time to live
     */
    private $ttl;
    
    /**
     *
     * @var object $ttl
     * @desctiption the memcach object
     */
    private $cache;
    

    public function __construct($ttl = 0, $host="127.0.0.1", $port=11211) { 
        $this->ttl = $ttl;
        $this->cache = new \Memcache;
        $this->cache->connect($host, $port);
    }

    /**
     * @name set
     * @description set value to cache
     * 
     * @param string $key cache label
     * @param string $value cache value
     * @return bool
     */
    public function set($key, $value) {
        return $this->cache->set($key, $value, MEMCACHE_COMPRESSED, $this->ttl);
    }

    /**
     * @name get
     * @description get value from cache
     * 
     * @param string $key cache label
     * @return mixed
     */
    public function get($key) {
        return  $this->cache->get($key);
    }

    /**
     * @name delete
     * @description delete cache by label

     * @param string $key cache label
     * @return bool
     */
    public function delete($key) {
        return $this->cache->delete($key);
    }

    /**
     * @name exists
     * @description exists value in cache
     * 
     * @param string $key cache label
     * @return bool
     */
    public function exists($key) {
        return ($this->cache->get($key) != false)?true:false;
    }

}
