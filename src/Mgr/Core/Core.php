<?php

/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */

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
    
    
        
    
    /**
     * @type Array
     * @name $selectedRoute 
     * @description The selected route from route - initialization after router despach;
     */
    public  $selectedRoute = null;
    
    
            
    function __construct() {
        $this->configuration = \Application\Config\Configurator::config();
        $this->routes = \Application\Config\Routes::routes();
    }
    
    
    function dispach(){
        try{
            
            //init Router
            $router = new \Mgr\Router\Router($this->routes, $this->configuration);
            
            //dispach router
            \Mgr\Event\Event::trigger("router.preDispach");
            $this->selectedRoute = $router->dispach();
            \Mgr\Event\Event::trigger("router.postDispach", $this->selectedRoute);
           
            //create selected route namespace
            $selectedRouteNamespace = "\Application\Module\\".ucfirst($this->selectedRoute["module"]);
            $controllerName = $selectedRouteNamespace."\Controller\\".ucfirst($this->selectedRoute["controller"])."Controller"; 
            
            
            //init selected controller
            $controller = new $controllerName($selectedRouteNamespace);
            $controller->viewFolderPath = ROOT.str_replace("\\", DIRECTORY_SEPARATOR, $selectedRouteNamespace).DIRECTORY_SEPARATOR."View".DIRECTORY_SEPARATOR.ucfirst($this->selectedRoute["controller"]);
            $controller->selectedRoute = $this->selectedRoute;
            $controller->view = new \Mgr\View\View($controller->viewFolderPath.DIRECTORY_SEPARATOR.ucfirst($this->selectedRoute["action"]));
            $controller->getParams = $_GET;
            $controller->postParams = $_POST;
            $controller->params = isset( $this->selectedRoute["params"])? $this->selectedRoute["params"]: array();
            //fire The selected action
            $selectedAction = ucfirst($this->selectedRoute["action"])."Action";
            $controller->$selectedAction();
            
        }catch (\Exception $error){
            $error->getMessage();
            
        }catch (\Mgr\Exception\Route $error){
            $error->getMessage();
            
        }
        
  
    }
}
