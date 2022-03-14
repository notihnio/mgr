<?php
/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
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
    private array $routes = [];

    /**
     *
     * @var array the system configuration
     */
    private array $configuration;

    /**
     *
     * @param array $routes
     * @param array $configuration
     *
     * @throws \Mgr\Exception\Router
     */
    public function __construct($routes, $configuration) {
        if(!isset($routes)) {
            throw new \Mgr\Exception\Router("You new to specify a Routes array! If you use the default router please provide an empty array");
        }

        if(!isset($configuration)) {
            throw new \Mgr\Exception\Router("You new to specify a configuration array. Else please provide an empty array ");
        }

        $this->configuration = $configuration;
        $this->requestedPath = str_replace("index.php","",$_SERVER["REQUEST_URI"]); 
        $this->routes = $routes;
    }

    /**
     * @return array|bool array
     * @description handles default Routing Logic
     *
     */
    public function defaultRouter(): array|bool
    {
        $defaultModule = $this->configuration["defaults"]["module"] ?? "index";
        $defaultController = $this->configuration["defaults"]["controller"] ?? "index";
        $defaultAction = $this->configuration["defaults"]["action"] ?? "index";
        
        //check for get parameters
        $requestedPathExpodes = explode("?", $this->requestedPath);
        $this->requestedPath = $requestedPathExpodes[0];
        
        //if is / then return defaults
        if ($this->requestedPath === "/") {
            return [
                "module" => $defaultModule,
                "controller" => $defaultController,
                "action" => $defaultAction
            ];
        }

        $requestedPath = trim($this->requestedPath, "/");

        // explode / 
        $explodedPathElements = explode("/", $requestedPath);



        $paramsNum = count($explodedPathElements);

        //reset array keys
        $explodedPathElements = array_values($explodedPathElements);

        // if path  = /
        if ($paramsNum === 0) {
            return [
                "module" => $defaultModule,
                "controller" => $defaultController,
                "action" => $defaultAction
            ];
        }


        // is one param
        if ($paramsNum === 1) {
            return [
                "module" => $explodedPathElements[0],
                "controller" => $defaultController,
                "action" => $defaultAction
            ];
        }

        // is one param
        if ($paramsNum === 2) {
            return [
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $defaultAction
            ];
        }

        if ($paramsNum === 3) {
            return [
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $explodedPathElements[2]
            ];
        }


        if ($paramsNum >= 4) {
            $router = [
                "module" => $explodedPathElements[0],
                "controller" => $explodedPathElements[1],
                "action" => $explodedPathElements[2],
            ];

            //remove controller module action params
            unset($explodedPathElements[0], $explodedPathElements[1], $explodedPathElements[2]);

            //reset array keys
            $explodedPathElements = array_values($explodedPathElements);
            $router["params"] = $this->getDefaultRouterURIParams($explodedPathElements);
            return $router;
        }
        
        return false;
    }

    /**
     * @return bool|array
     * @description handles normal Routing Logic
     *
     */
    public function normalRouter(): bool|array
    {

        //if there are no routes
        if (count($this->routes) <= 0)
            return false;

        //check for get parameters
        $requestedPathExplodes = explode("?", $this->requestedPath);
        $this->requestedPath = $requestedPathExplodes[0];
        
        // /article/3 to article/-  
        $requestedPathRegex = preg_replace("/\/[a-zA-Z0-9_-]{0,}/", "/-", trim($this->requestedPath, "/"));
         
        foreach ($this->routes as $route) {


            // check only in normal routes
            if (strtolower($route["type"]) === "normal") {

                //replace all variables ("/:variableName") from router routes with - and trim first and last slash so the form of the route no is article/- article/-/- etc
                $routerRegex = trim(preg_replace("/\/:[a-zA-Z0-9_-]{0,}/", "/-", $route["route"]), "/");
                
                //explode  category/post/-/* to /category/post
                $routerRegexParts = explode("/-", $routerRegex);
                $routerRegexStaticPart = "/".$routerRegexParts[0];
             
                //check if root regex ends with star so other params can be used at the end of th URL
                if (preg_match("/\*$/", $routerRegex)) {

                    //check if requested uri regex starts with the router regex
                    if (preg_match("/^" . str_replace("/", "\\/", rtrim($routerRegexStaticPart, "/*")) . ".*/", $this->requestedPath)) {


                        return [
                            "module" => strtolower($route["module"]),
                            "controller" => strtolower($route["controller"]),
                            "action" => strtolower($route["action"]),
                            "params" => $this->getNormalRouterParams($requestedPathRegex, $routerRegex, $route)
                        ];
                    }
                }

                //if there is no /* route check if requested url regex is the same with the router regex
                else {
                    //check if requested uri regex starts with the router regex
                     if (preg_match("/^" . str_replace("/", "\\/", rtrim($routerRegexStaticPart, "/*")) . ".*/", $this->requestedPath)) {
                        return [
                            "module" => strtolower($route["module"]),
                            "controller" => strtolower($route["controller"]),
                            "action" => strtolower($route["action"]),
                            "params" => $this->getNormalRouterParams($requestedPathRegex, $routerRegex, $route)
                        ];
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
     * @description "Returns Url  parameters array form array(paramName => paramValue)"
     *  
     * @param array $paramsArray the parameters array form array("paramname1", "paramvalue1", "paramName2, "paramValue2")
     * @return array
     */
    public function getDefaultRouterURIParams(array $paramsArray): array
    {

        $params = [];

        for ($counter = 0, $counterMax = count($paramsArray); $counter < $counterMax; $counter+=2) {
            $params[$paramsArray[$counter]] = ($paramsArray[$counter + 1] ?? null);
        }

        return $params;
    }

    /**
     * 
     * @param string $requestedPathRegex - the requested path regex
     * @param string $routerRegex        the router regex
     * @param array  $route              the array with rout configuration
     *
     * @return array Controller Module Action and url params
     */
    public function getNormalRouterParams(string $requestedPathRegex, string $routerRegex, array $route): array
    {
        //get static part of route  /article/:article_id => /article/
        $staticPartOfTheRoute = preg_replace("/:.{0,}/", "", $route["route"]);

        // explode url at / to get params
        $urlParamsValues = explode("/", rtrim(preg_replace("/" . str_replace("/", "\\/", $staticPartOfTheRoute) . "/", "", $this->requestedPath, 1), "/"));

        // explode url at / to get the map of params ex. :paramName from route or the /* params
        $routeParams = explode("/", preg_replace("/" . str_replace("/", "\\/", $staticPartOfTheRoute) . "/", "", $route["route"], 1));

        //stores the exported parameters
        $params = [];

        //for each route param
        for ($counter = 0, $counterMax = count($routeParams); $counter < $counterMax; $counter++) {
            if (preg_match("/:[a-zA-Z0-9_\-]{0,}/", $routeParams[$counter])) {
                $params[preg_replace("/:/", "", $routeParams[$counter])] = $urlParamsValues["$counter"];
                unset($urlParamsValues[$counter]);
            }

            if ($routeParams[$counter] === "*") {
                $defaultRouterParams = $this->getDefaultRouterURIParams(array_values($urlParamsValues));
                return array_merge($params, $defaultRouterParams);
            }
        }
        return $params;
    }

    /**
     * @return array|bool|void
     */
    public function dispatch()
    {

        if ($this->normalRouter()) {
            return $this->normalRouter();
        }

        if ($this->defaultRouter()) {
            return $this->defaultRouter();
        }
    }

}
