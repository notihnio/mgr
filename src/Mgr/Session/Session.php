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
    private string $namespace;

    /**
     * @description Does the construction job
     *
     * @param string $namespace - the PHP session namespace
     *
     * @throws \Mgr\Exception\Session
     */
    public function __construct(string $namespace) {
        ob_start();
        $this->namespace = $namespace;
        session_name($this->namespace);
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (!session_start()) {
                throw new \Mgr\Exception\Session("Session did not initialized");
            }
        }
        ob_get_clean();

    }

    /**
     * 
     * @description returns the session namespace
     * 
     * @return string
     */
    public function getSessionNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @description set a key value pair to session
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        $_SESSION[$key] = $value;
        if ($_SESSION[$key]) {
            return true;
        }
        return false;
    }

    /**
     * @description returns the requested session key, value
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * @description regenerates session id
     *
     * @param string $key - the PHP session key
     * 
     * @return bool
     */
    public function regenerateId(string $key): bool
    {
        return session_regenerate_id();
    }

    /**
     * @description destroy session by name
     *
     * @return bool
     */
    public function destroy() : bool
    {
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
