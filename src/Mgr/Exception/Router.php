<?php
/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */

namespace Mgr\Exception;

use JetBrains\PhpStorm\Pure;

/**
 * @class  Mgr\Exception\Router
 * @Description Handles Route exception
 */
class Router extends \Exception {

    // Redefine the exception so message isn't optional
    #[Pure] public function __construct($message, $code = 0, \Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
