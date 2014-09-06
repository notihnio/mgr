<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\Session;

/**
 * @class Session
 * @description Handles PHP Session
 * 
 */
class Session {

    /**
     *
     * @var string
     * @description the session namespace
     */
    private $namespace;

    /**
     * @name Constructor   
     * @description Does the construction job
     * @param type {Sring} $namespace - the PHP session namespace
     */
    public function __construct($namespace) {
        ob_start();
        $this->namespace = $namespace;
        session_name($this->namespace);
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (!session_start())
                throw new \Mgr\Exception\Session("Session did not initialized");
        }
        ob_get_clean();

    }

    /**
     * 
     * @name getSessionNamespace
     * @description returns the session namespace
     * 
     * @return sting
     */
    public function getSessionNamespace() {
        return $this->namespace;
    }

    /**
     * @name set   
     * @description set a key value pair to session
     * 
     * @param type {String} $key - the PHP session key
     * @param type {String} $value - the PHP session value
     * 
     * @return Boolean
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
        if ($_SESSION[$key])
            return true;
        return false;
    }

    /**
     * @name get   
     * @description returns the requested session key, value
     *
     * @param type $key - the PHP session key
     * 
     * @return {mixed} string if value exists ot false when key does not existe
     */
    public function get($key) {
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        return false;
    }

    /**
     * @name regenerateId   
     * @description regenerates session id
     *
     * @param type $key - the PHP session key
     * 
     * @return {mixed} string if value exists ot false when key does not existe
     */
    public function regenerateId($key) {
        return session_regenerate_id();
    }

    /**
     * @name destroy   
     * @description destroy session by name
     *
     * @return void
     */
    public function destroy() {
        return session_destroy();
    }

    /**
     * @name destruction   
     * @description does the destruction job
     *
     */
    public function __destruct() {
        session_write_close();
    }

}
