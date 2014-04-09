<?php

namespace Mgr\Core;

/**
 * @class Configurator
 * @namespace Mgr\Core
 *
 * @description Does everything - is the whole app
 * @author notihnio
 */

class Core {
    
    /**
     *
     * @type Array
     * @name $configuration 
     * @description handles the configuration;
     */
    private $configuration;
    
    function __construct() {
        $this->configuration = \Application\Config\Configurator::config();
    }
    
    
    function dispach(){
        $router = new \Mgr\Router\Router(\Application\Config\Routes::routes());
        die(var_dump($router->dispach()));
    }
}
