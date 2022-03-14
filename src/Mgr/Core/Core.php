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
    private array $configuration;

    /**
     *
     * @type Array
     * @name routes 
     * @description RouterRoutes;
     */
    private array $routes = array();

    /**
     * @type Array
     * @name $selectedRoute 
     * @description The selected route from route - initialization after router despach;
     */
    public array|null $selectedRoute = null;


    public function __construct() {
        $this->configuration = \Application\Config\Configurator::config();   
        
        //check for local route file in module directories
        $path = ROOT.DIRECTORY_SEPARATOR."Application".DIRECTORY_SEPARATOR."Module"; // '.' for current
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot())
                continue;

            if ($file->isDir()) {
                $moduleName = $file->getFilename();
                
                //check if Module/Config/Routes file exists
                $moduleRoutesClassPath = ROOT.DIRECTORY_SEPARATOR."Application".DIRECTORY_SEPARATOR."Module".DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR."Config".DIRECTORY_SEPARATOR."Routes.php";
                if(file_exists($moduleRoutesClassPath)){
                    //get module local routes
                    $moduleRouteClassName =  "\\Application\\Module\\{$moduleName}\\Config\\Routes";
                    $moduleRoutes = $moduleRouteClassName::routes();
                    $this->routes = array_merge($this->routes, $moduleRoutes);
                }
                 
            }
        }
       
        //add global routes
        $this->routes = array_merge($this->routes, \Application\Config\Routes::routes());
    }

    /**
     * @return void
     */
    public function dispatch() : void
    {
        try {

            //init Router
            $router = new \Mgr\Router\Router($this->routes, $this->configuration);

            //dispatch router
            \Mgr\Event\Event::trigger("router.preDispatch");
            $this->selectedRoute = $router->dispatch();
            \Mgr\Event\Event::trigger("router.postDispatch", $this->selectedRoute);

            //create selected route namespace
            $selectedRouteNamespace = "\Application\Module\\" . ucfirst($this->selectedRoute["module"]);
            $controllerName = $selectedRouteNamespace . "\Controller\\" . ucfirst($this->selectedRoute["controller"]) . "Controller";


            //init selected controller
            $controller = new $controllerName($selectedRouteNamespace);
            $controller->viewFolderPath = ROOT . str_replace("\\", DIRECTORY_SEPARATOR, $selectedRouteNamespace) . DIRECTORY_SEPARATOR . "View" . DIRECTORY_SEPARATOR . ucfirst($this->selectedRoute["controller"]);
            $controller->selectedRoute = $this->selectedRoute;
            $controller->view = new \Mgr\View\View($controller->viewFolderPath . DIRECTORY_SEPARATOR . ucfirst($this->selectedRoute["action"]));
            $controller->getParams = $_GET;
            $controller->postParams = $_POST;
            $controller->params = $this->selectedRoute["params"] ?? [];
            //fire The selected action
            $selectedAction = ucfirst($this->selectedRoute["action"]) . "Action";

            //check if module local bootstrap class exists
            if(file_exists(ROOT.DIRECTORY_SEPARATOR."Application".DIRECTORY_SEPARATOR."Module".DIRECTORY_SEPARATOR.ucfirst($this->selectedRoute["module"]).DIRECTORY_SEPARATOR."Bootstrap.php")){
                $bootstrapClass = $selectedRouteNamespace."\\Bootstrap";
                // execute go
                $bootstrapClass::go();

            }

            \Mgr\Event\Event::trigger("module.preDispatch");
            $controller->$selectedAction();
            \Mgr\Event\Event::trigger("module.postDispatch");
        } catch (\Exception $error) {
            $error->getMessage();
        } catch (\Mgr\Exception\Route $error) {
            $error->getMessage();
        }
    }

}
