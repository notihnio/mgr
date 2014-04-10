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
    
    
    /**
     *
     * @type Array
     * @name routes 
     * @description RouterRoutes;
     */
    private $routes;
    
    
    
    function __construct() {
        $this->configuration = \Application\Config\Configurator::config();
        $this->routes = \Application\Config\Routes::routes();
    }
    
    
    function dispach(){
        try{
            
            //init Router
            $router = new \Mgr\Router\Router($this->routes, $this->configuration);
            
            \Mgr\Event\Event::trigger("router.preDispach");
            $routeParams = $router->dispach();
            \Mgr\Event\Event::trigger("router.postDispach", $routeParams);
            
            
        }catch (\Exception $error){
            $error->getMessage();
            
        }catch (\Mgr\Exception\Route $error){
            $error->getMessage();
            
        }
        
  
    }
}
