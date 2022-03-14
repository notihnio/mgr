<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\Controller;

use JetBrains\PhpStorm\Pure;
use \Mgr\View\View;
use Mgr\Layout\Layout;

/**
 * @name Controller 
 * @description Handles Mvc Controller Logic
 * 
 */
class Controller {

    /**
     * @var View View
     * @description View object
     * 
     */
    public View $view;

    /**
     * @var string $viewFolderPath
     * @description the view folder path
     * 
     */
    public string $viewFolderPath;

    /**
     * @var array $selectedRoute
     * @description the router selected route
     * 
     */
    public array $selectedRoute;

    /**
     * @var Layout Layout
     * @description Layout object
     * 
     */
    public Layout $layout;

    /**
     *
     * @var array $postParams
     * @description handles postParams
     */
    private array $postParams;

    /**
     *
     * @var array $params
     * @description handles router params
     */
    public array $params;

    /**
     *
     * @var array $getParams
     * @description handles post params
     */
    public array $getParams;

    #[Pure] public function __construct() {
        $this->layout = new Layout();
    }

    public function __destruct() {
        try {
            if (isset($this->layout->__Name)) {
                $this->layout->layoutFilePath = dirname($this->viewFolderPath, 2) . DIRECTORY_SEPARATOR . "Layout" . DIRECTORY_SEPARATOR . ucfirst($this->layout->__Name);
                $this->layout->content = $this->view->render();
                $this->layout->render();
            } else {
                echo $this->view->render();
            }
        } catch (\Exception $error) {
            return;
        }
    }

}
