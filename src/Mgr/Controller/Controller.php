<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\Controller;

/**
 * @name Controller 
 * @description Handles Mvc Controller Logic
 * 
 */
class Controller {

    /**
     * @var \Mgr\View\View View
     * @description Thes view object
     * 
     */
    public $view;

    /**
     * @var sting $viewFolderPath
     * @description the view folder path
     * 
     */
    public $viewFolderPath;

    /**
     * @var array $selectedRoute
     * @description the router selected route
     * 
     */
    public $selectedRoute;

    /**
     * @var \Mgr\Layout\Layout Layout
     * @description Thes layout objects object
     * 
     */
    public $layout;

    /**
     *
     * @var array $postParams
     * @description handles postParams
     */
    private $postParams;

    /**
     *
     * @var array $Params
     * @description handles router params
     */
    public $params;

    /**
     *
     * @var array $getParams
     * @description handles post params
     */
    public $getParams;

    public function __construct() {
        $this->layout = new \Mgr\Layout\Layout();
    }

    public function __destruct() {
        try {
            if (isset($this->layout->__Name)) {
                $this->layout->__layoutFilePath = dirname(dirname($this->viewFolderPath)) . DIRECTORY_SEPARATOR . "Layout" . DIRECTORY_SEPARATOR . ucfirst($this->layout->__Name);
                $this->layout->content = $this->view->render();
                $this->layout->render();
            } else {
                echo $this->view->render();
            }
        } catch (\Mgr\Exception $error) {
            return;
        }
    }

}
