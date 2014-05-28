<?php
/**
    @author Panagiotis Mastrandrikos <pmstrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */
namespace Mgr\Cache;

/**
 * @name Cache 
 * @description Handles Caching behaviour
 * 
 */
interface CacheInterface {

    /**
     * @name get
     * @description get value from cache
     * 
     * @param string $key cache label
     * @return mixed
     */
    public function get($key);
    
    
    /**
     * @name set
     * @description set value to cache
     * 
     * @param string $key cache label
     * @param string $value cache value
     * @return bool
     */
    public function set($key, $value);
    
    
    /**
     * @name delete
     * @description delete cache by label
     
     * @param string $key cache label
     * @return bool
     */
    public function delete($key);
    
    
    /**
     * @name exists
     * @description exists value in cache
     * 
     * @param string $key cache label
     * @return bool
     */
    public function exists($key);
}
