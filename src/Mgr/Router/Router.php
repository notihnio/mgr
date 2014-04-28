<?php
/**
    @author Panagiotis Mastrandrikos <pmstrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */

namespace Mgr\Router;

/**
 * @class Router
 * @Description Implements the Routing logic for access MVC
 */
class Router {

    /**
     *
     * @var string the requested url
     */
    private $requestedPath;

    /**
     *
     * @var array the routes configuration
     */
    private $routes = array();

    /**
     *
     * @var array the system configuration
     */
    private $configuration;

    /**
     * 
     * @param array $routes 
     * @param array $configuration
     */
    public function __construct($routes, $configuration) {
        if(!isset($routes))
            throw new \Mgr\Exception\Router("You new to specify a Routes array! If you use the default router please provide an empty array");

        if(!isset($configuration))
             throw new \Mgr\Exception\Router("You new to specify a configuration array. Else please provide an empty array ");

        $this->configuration = $configuration;
        $this->requestedPath = str_replace("index.php","",$_SERVER["REQUEST_URI"]); 
        $this->routes = $routes;
    }

    /**
     * @name defaultRouter
     * @description handles default Routing Logic
     * 
     * @return type array
     */
    public function defaultRouter() {
        $defaultModule = (isset($this->configuration["defaults"]["module"])) ? $this->configuration["defaults"]["module"] : "index";
        $defaultController = (isset($this->configuration["defaults"]["controller"])) ? $this->configuration["defaults"]["controller"] : "index";
        $defaultAction = (isset($this->configuration["defaults"]["action"])) ? $this->configuration["defaults"]["action"] : "index";

        //if is / then return defaults
        if ($this->requestedPath == "/")
            return array(
                "module" => $defaultModule,
                "controller" => $defaultController,
                "action" => $defaultAction
            );

        $requestedPath = trim($this->requestedPath, "/");

        // explode / 
        $explodedPathElements = explode("/", $requestedPath);



        $paramsNum = count($explodedPathElements);

        //reset array keys
        $explodedPathElements = array_values($explodedPathElements);

        // if path  = /
        if ($paramsNum == 0)
            return array(
                "module" => $defaultModule,
                "controller" => $defaultController,
                "action" => $defaultAction
            );


        // is one param
        if ($paramsNum == 1)
            return array(
                "module" => $explodedPathElements[0],
                "controller" => $defaultController,
                "action" => $defaultAction
            );

        // is one param
        if ($paramsNum == 2)
            return array(
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $defaultAction
            );

        if ($paramsNum == 3)
            return array(
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $explodedPathElements[2]
            );


        if ($paramsNum >= 4) {
            $router = array(
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $explodedPathElements[2],
            );

            //remove controller module action params
            unset($explodedPathElements[0]);
            unset($explodedPathElements[1]);
            unset($explodedPathElements[2]);

            //reset array keys
            $explodedPathElements = array_values($explodedPathElements);
            $router["params"] = $this->getDefaultRouterURIParams($explodedPathElements);
            return $router;
        }
        
        return false;
    }

    /**
     * @name normalRouter
     * @description handles normal Routing Logic
     * 
     * @return type array
     */
    public function normalRouter() {

        //if there are no routes
        if (count($this->routes) <= 0)
            return false;

        // /article/3 to article/-  
        $requestedPathRegex = preg_replace("/\/[a-zA-Z0-9_-]{0,}/", "/-", trim($this->requestedPath, "/"));

        foreach ($this->routes as $route) {


            // check only in normal routes
            if (strtolower($route["type"]) == "normal") {

                //replace all variables ("/:variableName") from router routes with - and trim first and last slash so the form of the route no is article/- article/-/- etc
                $routerRegex = trim(preg_replace("/\/:[a-zA-Z0-9_-]{0,}/", "/-", $route["route"]), "/");


                //check if root regex ends with star so other params can be used at the end of th URL
                if (preg_match("/\*$/", $routerRegex)) {

                    //check if requested uri regex starts with the router regex
                    if (preg_match("/^" . str_replace("/", "\\/", trim($routerRegex, "/*")) . ".*/", $requestedPathRegex)) {


                        return array(
                            "module" => strtolower($route["module"]),
                            "controller" => strtolower($route["controller"]),
                            "action" => strtolower($route["action"]),
                            "params" => $this->getNormalRouterParams($requestedPathRegex, $routerRegex, $route)
                        );
                    }
                }

                //if there is no /* route check if requested url regex is the same with the router regex
                else {
                    //check if requestd uri regex starts with the router regex
                    if ($routerRegex == $requestedPathRegex) {
                        return array(
                            "module" => strtolower($route["module"]),
                            "controller" => strtolower($route["controller"]),
                            "action" => strtolower($route["action"]),
                            "params" => $this->getNormalRouterParams($requestedPathRegex, $routerRegex, $route)
                        );
                    }
                }


                //check route pattern
                //if (preg_match("/", $content)) {
                //}
            }
        }
        return false;
    }

    /**
     * @name getDefaultRouterURIParams
     * @description "Returns Url  parameters array form array(paramName => paramValue)"
     *  
     * @param array $paramsArray the parameters array form array("paramname1", "paramvalue1", "paramName2, "paramValue2")
     * @return array
     */
    public function getDefaultRouterURIParams(array $paramsArray) {

        $params = array();

        for ($counter = 0; $counter < count($paramsArray); $counter+=2) {
            $params[$paramsArray[$counter]] = (isset($paramsArray[$counter + 1]) ? $paramsArray[$counter + 1] : null);
        }

        return $params;
    }

    /**
     * 
     * @param string $requestedPathRegex - the requested path regex
     * @param type $routerRegex the router regex
     * @param type $route the array with rout configuration
     * @return array Controller Module Action and url params
     */
    public function getNormalRouterParams($requestedPathRegex, $routerRegex, $route) {
        //get static part of route  /article/:article_id => /article/
        $staticPartOfTheRoute = preg_replace("/:.{0,}/", "", $route["route"]);

        // explode url at / to get params
        $urlParamsValues = explode("/", rtrim(preg_replace("/" . str_replace("/", "\\/", $staticPartOfTheRoute) . "/", "", $this->requestedPath, 1), "/"));

        // explode url at / to get the map of params ex. :paramName from route or the /* params
        $routeParams = explode("/", preg_replace("/" . str_replace("/", "\\/", $staticPartOfTheRoute) . "/", "", $route["route"], 1));

        //stores the exported parameters
        $params = array();

        //for each route param
        for ($counter = 0; $counter < count($routeParams); $counter++) {
            if (preg_match("/:[a-zA-Z0-9_\-]{0,}/", $routeParams[$counter])) {
                $params[preg_replace("/:/", "", $routeParams[$counter])] = $urlParamsValues["$counter"];
                unset($urlParamsValues[$counter]);
            }

            if ($routeParams[$counter] == "*") {
                $defaultRouterParams = $this->getDefaultRouterURIParams(array_values($urlParamsValues));
                return array_merge($params, $defaultRouterParams);
            }
        }
        return $params;
    }

    public function dispach() {
        if ($this->normalRouter())
            return $this->normalRouter();

        if ($this->defaultRouter())
            return $this->defaultRouter();
    }

}
