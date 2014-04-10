<?php

namespace Mgr\Exception;

/**
 * @class  Mgr\Exception\Router
 * @Description Handles Route exception
 */
class Router extends \Exception {

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
